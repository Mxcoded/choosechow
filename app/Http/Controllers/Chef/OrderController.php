<?php

namespace App\Http\Controllers\Chef;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    // 1. List all orders for this Chef
    public function index()
    {
        // FIX: Added 'preparing' and 'ready' so active orders don't disappear
        $orders = Order::where('chef_id', Auth::id())
            ->whereIn('status', ['pending', 'preparing', 'ready', 'completed', 'cancelled'])
            ->with(['items', 'user']) // Eager load user to prevent "Attempt to read property of null"
            ->latest()
            ->paginate(10);

        return view('chef.orders.index', compact('orders')); 
    }

    // 2. Show Order Details
    public function show($id)
    {
        // We use findOrFail with eager loading for safety
        $order = Order::with(['items', 'user'])->findOrFail($id);

        if ($order->chef_id !== Auth::id()) {
            abort(403, "Unauthorized access to this order.");
        }

        return view('chef.orders.show', compact('order'));
    }

    // 3. Update Status (Unified Method)
    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        // 1. Security Check: Ensure this order belongs to the logged-in Chef
        if ($order->chef_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // 2. Validate (Ensure these match the values in your View forms)
        $request->validate([
            'status' => 'required|in:pending_payment,pending,preparing,ready,completed,cancelled'
        ]);

        // 3. Update Status
        $order->status = $request->status;
        $order->save();

        // 4. Redirect Back
        return redirect()->back()->with('success', 'Order status updated to ' . ucfirst($request->status));
    }
}