<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail; // <--- ADDED
use App\Services\PaystackService;
use App\Models\Transaction; 
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Wallet;
use App\Models\Menu; 
use App\Models\ChefProfile;
// --- MAIL CLASSES ---
use App\Mail\OrderReceiptMail;
use App\Mail\NewOrderAlertMail;

class CheckoutController extends Controller
{
    protected $paystack;

    public function __construct(PaystackService $paystack)
    {
        $this->paystack = $paystack;
    }

    public function index()
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('chef.index')->with('error', 'Your cart is empty.');
        }

        $subtotal = 0;
        foreach($cart as $details) {
            $subtotal += $details['price'] * $details['quantity'];
        }

        $deliveryFee = $this->getDeliveryFee($cart);
        $total = $subtotal + $deliveryFee;

        return view('customer.checkout.index', compact('cart', 'subtotal', 'deliveryFee', 'total'));
    }

    public function store(Request $request)
    {
        // 1. Validate Input
        $request->validate([
            'phone_number' => 'required',
            'address' => 'required',
        ]);

        $user = Auth::user();
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('chef.index')->with('error', 'Cart is empty');
        }

        try {
            // 2. Validate all menu items are available
            foreach($cart as $menuId => $details) {
                $menu = Menu::find($menuId);
                if (!$menu || !$menu->is_available) {
                    return back()->with('error', "The item \"{$details['name']}\" is no longer available. Please update your cart.");
                }
            }

            // 3. Group cart items by chef
            $cartByChef = $this->groupCartByChef($cart);
            
            // 4. Calculate totals
            $subtotal = 0;
            foreach($cart as $details) {
                $subtotal += $details['price'] * $details['quantity'];
            }
            
            // 4. Calculate delivery fee (sum of all chef delivery fees)
            $totalDeliveryFee = 0;
            foreach($cartByChef as $chefId => $items) {
                $totalDeliveryFee += $this->getChefDeliveryFee($chefId);
            }
            
            $totalAmount = $subtotal + $totalDeliveryFee;

            // 5. Generate Reference (for payment)
            $reference = 'CHOW-' . strtoupper(uniqid());

            // 6. Initialize Paystack
            $paymentData = $this->paystack->initializeTransaction($user->email, $totalAmount, $reference);

            if (!$paymentData['status']) {
                return back()->with('error', 'Payment failed to initialize. Details: ' . ($paymentData['message'] ?? 'Unknown error'));
            }

            // 7. Create Orders (one per chef) - grouped under same payment reference
            $createdOrders = [];
            foreach($cartByChef as $chefId => $items) {
                $chefSubtotal = 0;
                foreach($items as $item) {
                    $chefSubtotal += $item['price'] * $item['quantity'];
                }
                $chefDeliveryFee = $this->getChefDeliveryFee($chefId);
                $chefTotal = $chefSubtotal + $chefDeliveryFee;

                $order = Order::create([
                    'order_number' => $reference . '-' . strtoupper(substr(uniqid(), -4)), // e.g., CHOW-xxx-ABCD
                    'user_id' => $user->id,
                    'chef_id' => $chefId,
                    'total_amount' => $chefTotal,
                    'status' => 'pending_payment',
                    'payment_status' => 'pending',
                    'payment_method' => 'paystack',
                    'delivery_address' => $request->address,
                    'phone_number' => $request->phone_number,
                    'notes' => $request->input('notes'),
                ]);

                // Save order items for this chef
                foreach($items as $id => $details) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'menu_id' => $id,
                        'menu_name' => $details['name'],
                        'price' => $details['price'],
                        'quantity' => $details['quantity'],
                    ]);
                }
                
                $createdOrders[] = $order;
            }

            // 8. Store payment reference in session to track all orders
            session()->put('payment_reference', $reference);
            session()->put('related_orders', collect($createdOrders)->pluck('id')->toArray());

            // 9. Redirect to payment
            return redirect($paymentData['data']['authorization_url']);

        } catch (\Exception $e) {
            return back()->with('error', 'System error: ' . $e->getMessage());
        }
    }

    public function handleGatewayCallback(Request $request)
    {
        $reference = $request->query('reference');

        if (!$reference) {
            return redirect()->route('cart.index')->with('error', 'No payment reference found.');
        }

        try {
            // SECURITY: Verify Paystack Signature (Disabled in development for testing)
            if (env('APP_ENV') === 'production') {
                $signature = $request->header('X-Paystack-Signature');
                if (!$signature || !$this->paystack->verifySignature($signature)) {
                    \Log::warning('Invalid Paystack signature for reference: ' . $reference);
                    return redirect()->route('cart.index')->with('error', 'Payment verification failed. Invalid signature.');
                }
            } else {
                \Log::info('Skipping signature verification in development mode for reference: ' . $reference);
            }

            // Verify Transaction with Paystack
            $verification = $this->paystack->verifyTransaction($reference);

            // Check if transaction is valid and successful
            if (!$this->paystack->isTransactionSuccessful($verification)) {
                \Log::warning('Payment verification failed for reference: ' . $reference);
                return redirect()->route('cart.index')->with('error', 'Payment failed. Please try again.');
            }

            // IMPORTANT: Validate payment amount matches expected amount
            $orders = Order::where('order_number', 'like', $reference . '%')
                ->where('payment_status', 'pending')
                ->get();

            if ($orders->isEmpty()) {
                \Log::error('No orders found for reference: ' . $reference);
                return redirect()->route('cart.index')->with('error', 'Order not found.');
            }

            // Calculate total expected amount from orders
            $expectedTotal = $orders->sum('total_amount');
            $actualPaid = $verification['data']['amount'] / 100; // Convert from Kobo

            // Validate amount matches
            if (!$this->paystack->validatePaymentAmount($verification['data']['amount'], $expectedTotal)) {
                \Log::error("Payment amount mismatch. Expected: {$expectedTotal}, Received: {$actualPaid} for reference: {$reference}");
                return redirect()->route('cart.index')->with('error', 'Payment amount does not match. Please contact support.');
            }

            // All verification passed - process payment
            DB::transaction(function () use ($reference, $orders) {
                
                $orders->load('user', 'chef');
                
                foreach($orders as $order) {
                    // Check if already processed
                    if ($order->payment_status === 'paid') {
                        \Log::warning('Order already paid: ' . $order->order_number);
                        continue;
                    }

                    // A. Mark Order as Paid and Pending
                    $order->update([
                        'status' => 'pending',
                        'payment_status' => 'paid'
                    ]);

                    // B. Wallet Logic - Add earnings to each chef with audit trail
                    $platformFee = $order->total_amount * 0.05; // 5% platform commission
                    $chefEarnings = $order->total_amount - $platformFee;

                    $chefWallet = Wallet::firstOrCreate(
                        ['user_id' => $order->chef_id],
                        ['balance' => 0]
                    );
                    
                    // Log transaction with complete audit trail
                    $chefWallet->logTransaction(
                        'earning',
                        $chefEarnings,
                        $order->order_number,
                        "Order #{$order->order_number} - Platform fee: ₦" . number_format($platformFee, 2)
                    );

                    // C. Log Transaction for each chef (kept for backwards compatibility)
                    Transaction::create([
                        'user_id' => $order->chef_id,
                        'type' => 'earning',
                        'amount' => $chefEarnings,
                        'reference' => $reference,
                        'description' => "Order #{$order->order_number}",
                        'status' => 'completed'
                    ]);

                    // D. Send Alert to Chef
                    try {
                        Mail::to($order->chef->email)->send(new NewOrderAlertMail($order));
                    } catch (\Exception $e) {
                        \Log::error('Chef Alert Email Error: ' . $e->getMessage());
                    }
                }

                // E. Send Receipt to Customer (once, with all orders)
                $customer = $orders->first()->user;
                try {
                    // Email with all orders information
                    \Mail::raw("Your orders have been placed successfully. Order numbers: " . $orders->pluck('order_number')->implode(', '), function ($msg) use ($customer) {
                        $msg->to($customer->email)
                            ->subject('Order Confirmation - ChooseChow');
                    });
                } catch (\Exception $e) {
                    \Log::error('Receipt Email Error: ' . $e->getMessage());
                }

                // F. Clear Cart
                session()->forget('cart');
                session()->forget('payment_reference');
                session()->forget('related_orders');
                session()->save();
            });

            return redirect()->route('customer.orders')->with('success', 'Payment successful! Your orders have been placed.');

        } catch (\Exception $e) {
            \Log::error('Payment callback error: ' . $e->getMessage());
            return redirect()->route('cart.index')->with('error', 'Payment processing error: ' . $e->getMessage());
        }
    }

    /**
     * Group cart items by chef_id
     * Returns: ['chef_id' => [menu_id => details, ...], ...]
     */
    private function groupCartByChef($cart)
    {
        $grouped = [];
        
        foreach ($cart as $menuId => $details) {
            $menu = Menu::find($menuId);
            if ($menu) {
                $chefId = $menu->user_id;
                if (!isset($grouped[$chefId])) {
                    $grouped[$chefId] = [];
                }
                $grouped[$chefId][$menuId] = $details;
            }
        }
        
        return $grouped;
    }

    /**
     * Get delivery fee for a specific chef
     */
    private function getChefDeliveryFee($chefId)
    {
        $chefProfile = ChefProfile::where('user_id', $chefId)->first();
        
        if ($chefProfile) {
            return $chefProfile->delivery_fee;
        }
        
        return 1500; // Default delivery fee
    }

    /**
     * Legacy method - kept for backward compatibility
     */
    private function getDeliveryFee($cart)
    {
        $firstItemId = array_key_first($cart);
        $firstMenu = Menu::find($firstItemId);
        
        if ($firstMenu) {
            return $this->getChefDeliveryFee($firstMenu->user_id);
        }
        return 1500; // Default
    }
}