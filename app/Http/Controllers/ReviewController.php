<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        $order = Order::findOrFail($request->order_id);

        if ($order->user_id !== Auth::id()) {
            abort(403, "This is not your order.");
        }
        if ($order->status !== 'completed') {
            return back()->with('error', 'You can only review delivered orders.');
        }
        if (Review::where('order_id', $order->id)->exists()) {
            return back()->with('error', 'You have already reviewed this order.');
        }

        // FIX: Fill BOTH user_id and customer_id with the same Auth::id()
        Review::create([
            'user_id' => Auth::id(),      // Satisfies one column
            'customer_id' => Auth::id(),  // Satisfies the other column
            'chef_id' => $order->chef_id,
            'order_id' => $order->id,
            'rating' => $request->rating,
            'comment' => $request->comment
        ]);

        return back()->with('success', 'Thank you for your review!');
    }
}