<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Http\Resources\OrderResource;
use App\Models\Cart;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    use ApiResponse;

    /**
     * Get user's order history.
     * 
     * GET /api/v1/orders
     */
    public function index(Request $request)
    {
        $query = Order::where('user_id', $request->user()->id)
            ->with(['items.menu', 'chef.chefProfile']);

        // Filter by status
        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        // Filter by date range
        if ($from = $request->get('from_date')) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to = $request->get('to_date')) {
            $query->whereDate('created_at', '<=', $to);
        }

        $perPage = min($request->get('per_page', 15), 50);
        $orders = $query->orderByDesc('created_at')->paginate($perPage);

        return $this->successWithPagination(
            $orders->through(fn($order) => new OrderResource($order))
        );
    }

    /**
     * Get active orders (not completed/cancelled).
     * 
     * GET /api/v1/orders/active
     */
    public function active(Request $request)
    {
        $orders = Order::where('user_id', $request->user()->id)
            ->whereNotIn('status', ['completed', 'cancelled', 'delivered'])
            ->with(['items.menu', 'chef.chefProfile'])
            ->orderByDesc('created_at')
            ->get();

        return $this->success(OrderResource::collection($orders));
    }

    /**
     * Create order from cart with Paystack payment.
     * 
     * POST /api/v1/orders
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'address_id' => ['required_without:delivery_address', 'exists:user_addresses,id'],
            'delivery_address' => ['required_without:address_id', 'string', 'max:500'],
            'phone_number' => ['required', 'string', 'max:20'],
            'notes' => ['nullable', 'string', 'max:500'],
            'delivery_type' => ['required', 'in:asap,scheduled'],
            'scheduled_date' => ['required_if:delivery_type,scheduled', 'date', 'after_or_equal:today'],
            'scheduled_time_slot' => ['required_if:delivery_type,scheduled', 'string'],
            'payment_method' => ['required', 'in:card,bank_transfer'],
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $user = $request->user();

        // Get cart items
        $cartItems = Cart::where('user_id', $user->id)
            ->with(['menu.chef.chefProfile'])
            ->get();

        if ($cartItems->isEmpty()) {
            return $this->error('Your cart is empty', 400);
        }

        // Validate all items are still available
        foreach ($cartItems as $item) {
            if (!$item->menu->is_available) {
                return $this->error("'{$item->menu->name}' is no longer available", 400);
            }
            if (!$item->menu->chef->chefProfile?->is_online) {
                return $this->error("Chef for '{$item->menu->name}' is not accepting orders", 400);
            }
        }

        // Get delivery address
        $deliveryAddress = $request->delivery_address;
        if ($request->address_id) {
            $address = $user->addresses()->find($request->address_id);
            if ($address) {
                $deliveryAddress = implode(', ', array_filter([
                    $address->street_address,
                    $address->apartment,
                    $address->city,
                    $address->state,
                ]));
            }
        }

        // Group cart items by chef (create separate order per chef for multi-vendor)
        $groupedByChef = $cartItems->groupBy(fn($item) => $item->menu->user_id);

        try {
            DB::beginTransaction();

            $orders = [];
            $totalAmount = 0;

            foreach ($groupedByChef as $chefId => $items) {
                $chef = $items->first()->menu->chef;
                $chefProfile = $chef->chefProfile;

                // Calculate totals
                $subtotal = $items->sum(fn($item) => $item->menu->price * $item->quantity);
                $deliveryFee = (float) ($chefProfile?->delivery_fee ?? 0);
                $orderTotal = $subtotal + $deliveryFee;
                $totalAmount += $orderTotal;

                // Check minimum order
                $minimumOrder = (float) ($chefProfile?->minimum_order ?? 0);
                if ($subtotal < $minimumOrder) {
                    DB::rollBack();
                    return $this->error(
                        "Minimum order for {$chefProfile?->business_name} is ₦" . number_format($minimumOrder, 2),
                        400
                    );
                }

                // Create order
                $order = Order::create([
                    'order_number' => 'CC-' . strtoupper(Str::random(8)),
                    'user_id' => $user->id,
                    'chef_id' => $chefId,
                    'subtotal' => $subtotal,
                    'delivery_fee' => $deliveryFee,
                    'total_amount' => $orderTotal,
                    'status' => 'pending_payment',
                    'payment_status' => 'pending',
                    'payment_method' => $request->payment_method,
                    'delivery_address' => $deliveryAddress,
                    'phone_number' => $request->phone_number,
                    'notes' => $request->notes,
                    'delivery_type' => $request->delivery_type,
                    'scheduled_date' => $request->scheduled_date,
                    'scheduled_time_slot' => $request->scheduled_time_slot,
                ]);

                // Create order items
                foreach ($items as $cartItem) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'menu_id' => $cartItem->menu_id,
                        'menu_name' => $cartItem->menu->name,
                        'quantity' => $cartItem->quantity,
                        'price' => $cartItem->menu->price,
                        'special_instructions' => $cartItem->special_instructions,
                    ]);
                }

                $orders[] = $order;
            }

            // Initialize Paystack payment
            $paymentData = $this->initializePaystackPayment($user, $totalAmount, $orders);

            if (!$paymentData['success']) {
                DB::rollBack();
                return $this->error($paymentData['message'], 400);
            }

            // Store payment reference for all orders
            foreach ($orders as $order) {
                Payment::create([
                    'user_id' => $user->id,
                    'payable_type' => Order::class,
                    'payable_id' => $order->id,
                    'order_id' => $order->id, // Also store direct reference
                    'amount' => $order->total_amount,
                    'reference' => $paymentData['reference'],
                    'type' => 'order_payment',
                    'payment_method' => $request->payment_method,
                    'gateway' => 'paystack',
                    'status' => 'pending',
                ]);
            }

            // Clear cart
            Cart::where('user_id', $user->id)->delete();

            DB::commit();

            return $this->created([
                'orders' => OrderResource::collection(
                    collect($orders)->map(fn($o) => $o->fresh()->load(['items.menu', 'chef.chefProfile']))
                ),
                'payment' => [
                    'authorization_url' => $paymentData['authorization_url'],
                    'access_code' => $paymentData['access_code'],
                    'reference' => $paymentData['reference'],
                ],
                'total_amount' => $totalAmount,
                'formatted_total' => '₦' . number_format($totalAmount, 2),
            ], 'Order created. Please complete payment.');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('Failed to create order: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get a specific order.
     * 
     * GET /api/v1/orders/{id}
     */
    public function show(Request $request, $id)
    {
        $order = Order::where('user_id', $request->user()->id)
            ->with(['items.menu', 'chef.chefProfile'])
            ->where('id', $id)
            ->orWhere('order_number', $id)
            ->firstOrFail();

        return $this->success(new OrderResource($order));
    }

    /**
     * Cancel an order.
     * 
     * POST /api/v1/orders/{id}/cancel
     */
    public function cancel(Request $request, $id)
    {
        $order = Order::where('user_id', $request->user()->id)
            ->where(function ($q) use ($id) {
                $q->where('id', $id)->orWhere('order_number', $id);
            })
            ->firstOrFail();

        // Check if order can be cancelled
        if (!in_array($order->status, ['pending_payment', 'pending', 'confirmed'])) {
            return $this->error('This order cannot be cancelled', 400);
        }

        $order->update([
            'status' => 'cancelled',
        ]);

        // TODO: Process refund if payment was made

        return $this->success(
            new OrderResource($order->fresh()->load(['items.menu', 'chef.chefProfile'])),
            'Order cancelled successfully'
        );
    }

    /**
     * Reorder from a previous order.
     * 
     * POST /api/v1/orders/{id}/reorder
     */
    public function reorder(Request $request, $id)
    {
        $order = Order::where('user_id', $request->user()->id)
            ->with('items')
            ->where(function ($q) use ($id) {
                $q->where('id', $id)->orWhere('order_number', $id);
            })
            ->firstOrFail();

        $user = $request->user();
        $addedItems = [];
        $unavailableItems = [];

        foreach ($order->items as $item) {
            $menu = Menu::with('chef.chefProfile')->find($item->menu_id);

            if (!$menu || !$menu->is_available) {
                $unavailableItems[] = $item->name;
                continue;
            }

            if (!$menu->chef->chefProfile?->is_online) {
                $unavailableItems[] = $item->name . ' (chef offline)';
                continue;
            }

            // Add to cart or update existing
            $existingCartItem = Cart::where('user_id', $user->id)
                ->where('menu_id', $menu->id)
                ->first();

            if ($existingCartItem) {
                $existingCartItem->update([
                    'quantity' => $existingCartItem->quantity + $item->quantity,
                ]);
            } else {
                Cart::create([
                    'user_id' => $user->id,
                    'menu_id' => $menu->id,
                    'quantity' => $item->quantity,
                    'special_instructions' => $item->special_instructions,
                ]);
            }

            $addedItems[] = $item->name;
        }

        $message = count($addedItems) . ' item(s) added to cart.';
        if (!empty($unavailableItems)) {
            $message .= ' ' . count($unavailableItems) . ' item(s) were unavailable: ' . implode(', ', $unavailableItems);
        }

        return $this->success([
            'added_items' => $addedItems,
            'unavailable_items' => $unavailableItems,
        ], $message);
    }

    /**
     * Get available time slots for scheduling.
     * 
     * GET /api/v1/orders/time-slots
     */
    public function timeSlots(Request $request)
    {
        return $this->success([
            'time_slots' => Order::getAvailableTimeSlots(),
            'available_dates' => Order::getAvailableDates(7),
        ]);
    }

    /**
     * Initialize Paystack payment.
     */
    protected function initializePaystackPayment($user, $amount, $orders)
    {
        $paystackSecretKey = config('services.paystack.secret_key');

        if (!$paystackSecretKey) {
            return [
                'success' => false,
                'message' => 'Payment gateway not configured',
            ];
        }

        $reference = 'CC-PAY-' . strtoupper(Str::random(12));
        $orderNumbers = collect($orders)->pluck('order_number')->join(', ');

        try {
            $response = Http::withToken($paystackSecretKey)
                ->post('https://api.paystack.co/transaction/initialize', [
                    'email' => $user->email,
                    'amount' => $amount * 100, // Paystack expects amount in kobo
                    'reference' => $reference,
                    'callback_url' => config('app.url') . '/api/v1/payments/verify',
                    'metadata' => [
                        'user_id' => $user->id,
                        'order_numbers' => $orderNumbers,
                        'custom_fields' => [
                            [
                                'display_name' => 'Order Numbers',
                                'variable_name' => 'order_numbers',
                                'value' => $orderNumbers,
                            ],
                            [
                                'display_name' => 'Customer Name',
                                'variable_name' => 'customer_name',
                                'value' => $user->full_name,
                            ],
                        ],
                    ],
                ]);

            $data = $response->json();

            if ($response->successful() && $data['status']) {
                return [
                    'success' => true,
                    'authorization_url' => $data['data']['authorization_url'],
                    'access_code' => $data['data']['access_code'],
                    'reference' => $data['data']['reference'],
                ];
            }

            return [
                'success' => false,
                'message' => $data['message'] ?? 'Failed to initialize payment',
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Payment service error: ' . $e->getMessage(),
            ];
        }
    }
}
