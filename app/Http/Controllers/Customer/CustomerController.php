<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\ChefProfile;
use App\Models\User;
use App\Models\Cuisine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;

class CustomerController extends Controller
{
    /**
     * List all available chefs (Public).
     */
    public function index(Request $request)
    {
        $query = ChefProfile::query()
            ->where('is_online', true)
            ->where('verification_status', 'verified'); // Only verified chefs

        // Filter by Search Term
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('business_name', 'like', "%{$search}%")
                    ->orWhereHas('cuisines', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by Cuisine
        if ($request->has('cuisine')) {
            $query->whereHas('cuisines', function ($q) use ($request) {
                $q->where('slug', $request->cuisine);
            });
        }

        $chefs = $query->with(['user', 'cuisines'])->paginate(12);
        $cuisines = Cuisine::has('chefs')->get(); // Only show relevant cuisines

        return view('chefs.index', compact('chefs', 'cuisines'));
    }

    /**
     * Show a specific chef's storefront (Public).
     */
    public function show($id)
    {
        // Resolve by ID or Slug if you implemented slugs
        $chef = ChefProfile::where('id', $id)
            ->where('verification_status', 'verified')
            ->with(['user', 'cuisines', 'deliveryZones'])
            ->firstOrFail();

        // Load the Menu
        // We need the User model of the chef to get the menus
        $menus = $chef->user->menus()
            ->with(['category', 'cuisines'])
            ->where('is_available', true) // Only show available items
            ->get()
            ->groupBy('category.name'); // Group by category for nice display

        return view('chefs.show', compact('chef', 'menus'));
    }

    /**
     * Customer Dashboard (Protected).
     */
    public function dashboard()
    {
        $user = Auth::user();

        // 1. Calculate Stats
        $stats = [
            'total_orders' => Order::where('customer_id', $user->id)->count(),
            'total_spent' => Order::where('customer_id', $user->id)
                ->whereIn('payment_status', ['paid', 'completed'])
                ->sum('total_amount'),
            // Assuming you have a favorites relationship set up
            'favorite_chefs' => $user->favorites()->where('favoritable_type', 'App\Models\ChefProfile')->count(),
            'loyalty_points' => 0, // Placeholder for future logic
        ];

        // 2. Fetch Recent Orders (Real Data)
        $recentOrders = Order::where('customer_id', $user->id)
            ->with(['chef.chefProfile', 'items']) // Eager load chef details
            ->latest()
            ->take(5)
            ->get();

        return view('customers.dashboard', compact('user', 'stats', 'recentOrders'));
    }
    public function orders()
    {
        $orders = Order::where('customer_id', Auth::id())->latest()->paginate(10);
        return view('customers.orders.index', compact('orders'));
    }

    public function profile()
    {
        return view('customers.profile.edit', ['user' => Auth::user()]);
    }

    public function favorites()
    {
        // Placeholder
        return view('customers.favorites.index');
    }    
}
