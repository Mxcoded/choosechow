<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    use ApiResponse;

    /**
     * Get user's cart contents.
     * 
     * GET /api/v1/cart
     */
    public function index(Request $request)
    {
        $cartItems = Cart::where('user_id', $request->user()->id)
            ->with(['menu.chef.chefProfile'])
            ->get();

        // Group items by chef for multi-vendor support
        $groupedByChef = $cartItems->groupBy(fn($item) => $item->menu->user_id);

        $cartData = [];
        foreach ($groupedByChef as $chefId => $items) {
            $chef = $items->first()->menu->chef;
            $chefProfile = $chef->chefProfile;
            
            $subtotal = $items->sum(fn($item) => $item->menu->price * $item->quantity);
            
            $cartData[] = [
                'chef' => [
                    'id' => $chef->id,
                    'full_name' => $chef->full_name,
                    'business_name' => $chefProfile?->business_name,
                    'delivery_fee' => (float) ($chefProfile?->delivery_fee ?? 0),
                    'minimum_order' => (float) ($chefProfile?->minimum_order ?? 0),
                    'is_online' => $chefProfile?->is_online ?? false,
                ],
                'items' => CartResource::collection($items),
                'items_count' => $items->sum('quantity'),
                'subtotal' => $subtotal,
                'formatted_subtotal' => '₦' . number_format($subtotal, 2),
                'meets_minimum' => $subtotal >= ($chefProfile?->minimum_order ?? 0),
            ];
        }

        $totalItems = $cartItems->sum('quantity');
        $grandSubtotal = $cartItems->sum(fn($item) => $item->menu->price * $item->quantity);

        return $this->success([
            'chefs' => $cartData,
            'summary' => [
                'total_items' => $totalItems,
                'total_chefs' => count($cartData),
                'grand_subtotal' => $grandSubtotal,
                'formatted_grand_subtotal' => '₦' . number_format($grandSubtotal, 2),
            ],
        ]);
    }

    /**
     * Add item to cart.
     * 
     * POST /api/v1/cart/items
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'menu_id' => ['required', 'exists:menus,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:50'],
            'special_instructions' => ['nullable', 'string', 'max:500'],
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $menu = Menu::with('chef.chefProfile')->findOrFail($request->menu_id);

        // Validate menu is available
        if (!$menu->is_available) {
            return $this->error('This menu item is currently unavailable', 400);
        }

        // Validate chef is online
        if (!$menu->chef->chefProfile?->is_online) {
            return $this->error('This chef is currently not accepting orders', 400);
        }

        // Check if item already in cart
        $existingItem = Cart::where('user_id', $request->user()->id)
            ->where('menu_id', $request->menu_id)
            ->first();

        if ($existingItem) {
            // Update quantity
            $existingItem->update([
                'quantity' => $existingItem->quantity + $request->quantity,
                'special_instructions' => $request->special_instructions ?? $existingItem->special_instructions,
            ]);
            $cartItem = $existingItem->fresh()->load('menu.chef.chefProfile');
        } else {
            // Create new cart item
            $cartItem = Cart::create([
                'user_id' => $request->user()->id,
                'menu_id' => $request->menu_id,
                'quantity' => $request->quantity,
                'special_instructions' => $request->special_instructions,
            ]);
            $cartItem->load('menu.chef.chefProfile');
        }

        return $this->success(
            new CartResource($cartItem),
            'Item added to cart'
        );
    }

    /**
     * Update cart item quantity.
     * 
     * PUT /api/v1/cart/items/{id}
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => ['required', 'integer', 'min:1', 'max:50'],
            'special_instructions' => ['nullable', 'string', 'max:500'],
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $cartItem = Cart::where('user_id', $request->user()->id)
            ->where('id', $id)
            ->with('menu.chef.chefProfile')
            ->firstOrFail();

        // Validate menu is still available
        if (!$cartItem->menu->is_available) {
            return $this->error('This menu item is no longer available', 400);
        }

        $cartItem->update([
            'quantity' => $request->quantity,
            'special_instructions' => $request->special_instructions ?? $cartItem->special_instructions,
        ]);

        return $this->success(
            new CartResource($cartItem->fresh()->load('menu.chef.chefProfile')),
            'Cart updated'
        );
    }

    /**
     * Remove item from cart.
     * 
     * DELETE /api/v1/cart/items/{id}
     */
    public function destroy(Request $request, $id)
    {
        $cartItem = Cart::where('user_id', $request->user()->id)
            ->where('id', $id)
            ->firstOrFail();

        $cartItem->delete();

        return $this->success(null, 'Item removed from cart');
    }

    /**
     * Clear entire cart.
     * 
     * DELETE /api/v1/cart
     */
    public function clear(Request $request)
    {
        Cart::where('user_id', $request->user()->id)->delete();

        return $this->success(null, 'Cart cleared');
    }

    /**
     * Get cart summary for checkout.
     * 
     * GET /api/v1/cart/summary
     */
    public function summary(Request $request)
    {
        $cartItems = Cart::where('user_id', $request->user()->id)
            ->with(['menu.chef.chefProfile'])
            ->get();

        if ($cartItems->isEmpty()) {
            return $this->error('Your cart is empty', 400);
        }

        // Group by chef for delivery fee calculation
        $groupedByChef = $cartItems->groupBy(fn($item) => $item->menu->user_id);
        
        $subtotal = $cartItems->sum(fn($item) => $item->menu->price * $item->quantity);
        $totalDeliveryFee = 0;
        $chefSummaries = [];

        foreach ($groupedByChef as $chefId => $items) {
            $chefProfile = $items->first()->menu->chef->chefProfile;
            $chefSubtotal = $items->sum(fn($item) => $item->menu->price * $item->quantity);
            $deliveryFee = (float) ($chefProfile?->delivery_fee ?? 0);
            $minimumOrder = (float) ($chefProfile?->minimum_order ?? 0);
            
            $totalDeliveryFee += $deliveryFee;
            
            $chefSummaries[] = [
                'chef_id' => $chefId,
                'business_name' => $chefProfile?->business_name,
                'subtotal' => $chefSubtotal,
                'delivery_fee' => $deliveryFee,
                'minimum_order' => $minimumOrder,
                'meets_minimum' => $chefSubtotal >= $minimumOrder,
            ];
        }

        $grandTotal = $subtotal + $totalDeliveryFee;

        // Check if all minimum orders are met
        $allMinimumsMet = collect($chefSummaries)->every(fn($s) => $s['meets_minimum']);

        return $this->success([
            'items_count' => $cartItems->sum('quantity'),
            'chefs_count' => count($groupedByChef),
            'subtotal' => $subtotal,
            'delivery_fee' => $totalDeliveryFee,
            'grand_total' => $grandTotal,
            'formatted_subtotal' => '₦' . number_format($subtotal, 2),
            'formatted_delivery_fee' => '₦' . number_format($totalDeliveryFee, 2),
            'formatted_grand_total' => '₦' . number_format($grandTotal, 2),
            'can_checkout' => $allMinimumsMet,
            'chef_summaries' => $chefSummaries,
        ]);
    }
}
