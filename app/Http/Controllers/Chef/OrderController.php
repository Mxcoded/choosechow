<?php

namespace App\Http\Controllers\Chef;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function __construct()
    {
        // Use standard Spatie role middleware
        $this->middleware(['auth', 'role:chef']);
    }

    /**
     * Display a listing of the chef's orders.
     */
    public function index(Request $request)
    {
        $chef = Auth::user();
        $status = $request->get('status', 'all'); // Default to 'all' to see something initially

        $query = Order::where('chef_id', $chef->id);

        // Filter by status if not 'all'
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $orders = $query->latest()
            ->with(['customer', 'items'])
            ->paginate(15);

        // Calculate Stats
        $stats = [
            'pending' => Order::where('chef_id', $chef->id)->where('status', 'pending')->count(),
            'confirmed' => Order::where('chef_id', $chef->id)->where('status', 'confirmed')->count(),
            'preparing' => Order::where('chef_id', $chef->id)->where('status', 'preparing')->count(),
            'delivered' => Order::where('chef_id', $chef->id)->where('status', 'delivered')->count(),
            'all' => Order::where('chef_id', $chef->id)->count(),
        ];

        return view('chefs.orders.index', compact('orders', 'stats', 'status'));
    }

    /**
     * Display the specified order details.
     */
    public function show(Order $order)
    {
        if ($order->chef_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this order.');
        }

        $order->load(['customer', 'items.menu']);

        return view('chefs.orders.show', compact('order'));
    }

    /**
     * Update the status of the specified order.
     */
    public function updateStatus(Request $request, Order $order)
    {
        if ($order->chef_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'status' => 'required|in:confirmed,preparing,ready,out_for_delivery,delivered,cancelled'
        ]);

        $newStatus = $request->status;

        // Use the Model helpers we created
        if ($newStatus === 'confirmed') {
            $order->confirm();
        } elseif ($newStatus === 'delivered') {
            $order->markAsDelivered();
        } else {
            // For simple status changes
            $order->update(['status' => $newStatus]);

            // Optional: Set specific timestamps
            if ($newStatus === 'preparing') $order->update(['prepared_at' => now()]);
        }

        return response()->json([
            'success' => true,
            'message' => "Order #{$order->order_number} marked as " . ucfirst(str_replace('_', ' ', $newStatus))
        ]);
    }
}
