<?php

namespace App\Http\Controllers\Chef;

use App\Http\Controllers\Controller;
use App\Models\Order; 
use App\Models\User;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'chef']);
    }

    /**
     * Display a listing of the chef's orders.
     */
    public function index(Request $request)
    {
        $chef = Auth::user();
        $status = $request->get('status', 'pending'); // Default to pending

        // Orders query remains correct (paginated)
        $orders = Order::where('chef_id', $chef->id)
            ->when($status !== 'all', function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->latest()
            ->with(['customer', 'items'])
            ->paginate(15);

        // Define $stats using ONLY order-related counts
        $stats = [
            'pending' => Order::where('chef_id', $chef->id)->where('status', 'pending')->count(),
            'confirmed' => Order::where('chef_id', $chef->id)->where('status', 'confirmed')->count(),
            'delivered' => Order::where('chef_id', $chef->id)->where('status', 'delivered')->count(),
            'all' => Order::where('chef_id', $chef->id)->count(),
        ];

        // Pass $orders (Paginator) and $stats (Order counts) to the dedicated order index view
        return view('chefs.orders.index', compact('orders', 'stats', 'status'));
    }
    /**
     * Display the specified order details.
     */
    public function show(Order $order)
    {
        // Ensure the order belongs to the authenticated chef
        if ($order->chef_id !== Auth::id()) {
            abort(403);
        }

        $order->load(['customer', 'items.menu', 'payments']);

        return view('chefs.orders.show', compact('order'));
    }

    /**
     * Update the status of the specified order (e.g., Accept, Ready, Delivered).
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

        // Use model methods for status updates to ensure timestamps are set
        if ($newStatus === 'confirmed') {
            $order->confirm(); // Assumes confirm() method is implemented in Order model
        } elseif ($newStatus === 'delivered') {
            $order->markAsDelivered(); // Assumes markAsDelivered() method is implemented
        } else {
            // For other statuses (preparing, ready, out_for_delivery)
            $order->update(['status' => $newStatus]);
        }

        return response()->json(['success' => true, 'message' => "Order #{$order->order_number} status updated to " . ucfirst($newStatus)]);
    }

    // You might also add methods for cancellation logic here later
}
