<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Order;
use App\Models\ChefProfile; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class CustomerController extends Controller
{
    // 1. List all Chefs (UPDATED: Now handles Search & City Filtering)
    public function index(Request $request)
    {
        $query = ChefProfile::where('is_online', true)
                            ->with(['user.receivedReviews', 'cuisines']);

        // 1. Search Logic (Name, Cuisine, Address, City OR MENU ITEMS)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                // Check Profile
                $q->where('business_name', 'LIKE', "%{$search}%")
                  ->orWhere('bio', 'LIKE', "%{$search}%")
                  ->orWhere('kitchen_address', 'LIKE', "%{$search}%")
                  ->orWhere('city', 'LIKE', "%{$search}%")
                  ->orWhere('cuisines', 'LIKE', "%{$search}%")
                  
                  // FIX: Check Menu Items (Jollof, Burger, etc.)
                  ->orWhereHas('user.menus', function($subQ) use ($search) {
                      $subQ->where('name', 'LIKE', "%{$search}%")
                           ->orWhere('description', 'LIKE', "%{$search}%")
                           ->where('is_available', true);
                  });
            });
        }

        // 2. Filter by City
        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        $chefs = $query->latest()->paginate(12);

        return view('customer.chef.index', compact('chefs')); 
    }

    // 2. Show Chef Menu
    public function show($slug)
    {
        // Fetch Chef Profile + User + Available Menus + Cuisines
        $chef = ChefProfile::where('slug', $slug)
                            ->orWhere('id', $slug)
                            ->with(['user.receivedReviews', 'cuisines', 'user.menus' => function($query) {
                                $query->where('is_available', true);
                            }])
                            ->firstOrFail();

        $rawMenus = $chef->user->menus ?? collect();
        $menus = $rawMenus->groupBy('category_id');

        return view('customer.chef.show', compact('chef', 'menus'));
    }

    // 3. Customer Profile
    public function profile()
    {
        $user = Auth::user();
        return view('customer.profile.index', compact('user'));
    }

    // 4. My Orders List
    public function orders()
    {
        $orders = Auth::user()->ordersPlaced()
                      ->with('chef.chefProfile') // Important for displaying Kitchen Name
                      ->latest()
                      ->paginate(10);
                      
        return view('customer.orders.index', compact('orders'));
    }

    // 5. Show Single Order (Receipt)
    public function showOrder($id)
    {
        $order = Order::with(['items', 'chef.chefProfile', 'user'])
                      ->where('user_id', Auth::id()) // Security: Only own orders
                      ->findOrFail($id);

        return view('customer.orders.show', compact('order'));
    }

    // 6. Favorites
    public function favorites()
    {
        return view('customer.favorites.index');
    }

    // CANCEL ORDER
    public function cancelOrder(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if ($order->status == 'pending_payment' || $order->status == 'pending') {
            $order->update(['status' => 'cancelled']);
            return back()->with('success', 'Order cancelled successfully.');
        }

        return back()->with('error', 'This order cannot be cancelled as it is already processing.');
    }

    // RETRY PAYMENT
    public function retryPayment(Order $order)
    {
        if ($order->user_id !== Auth::id() || $order->status !== 'pending_payment') {
            abort(403);
        }

        try {
            $paystack = new \App\Services\PaystackService();
            $newRef = 'RETRY-' . strtoupper(uniqid());
            
            $order->update(['order_number' => $newRef]);

            $paymentData = $paystack->initializeTransaction(Auth::user()->email, $order->total_amount, $newRef);
            return redirect($paymentData['data']['authorization_url']);
        } catch (\Exception $e) {
            return back()->with('error', 'Payment service unavailable.');
        }
    }

    // UPDATE CUSTOMER PERSONAL PROFILE
    public function updateProfile(Request $request)
    {
        $user = User::findOrFail(Auth::id());

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20', // Kept as 'phone'
            'address' => 'required|string|max:500',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $data = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
        ];

        if (Schema::hasColumn('users', 'address')) {
            $data['address'] = $request->address;
        }

        if ($request->filled('password')) {
            $data['password'] = \Illuminate\Support\Facades\Hash::make($request->password);
        }

        $user->update($data);

        return back()->with('success', 'Profile updated successfully!');
    }
}