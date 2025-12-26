<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Menu;
use App\Models\Order;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            return $this->adminDashboard();
        } elseif ($user->hasRole('chef')) {
            return $this->chefDashboard();
        } else {
            return $this->customerDashboard();
        }
    }

    // --- CHEF DASHBOARD ---
    private function chefDashboard()
    {
        $chef = Auth::user();

        // 1. Base Queries
        $ordersQuery = Order::where('chef_id', $chef->id);

        // 2. Calculate Stats
        $stats = [
            'total_orders' => $ordersQuery->count(),
            'pending_orders' => $ordersQuery->clone()->whereIn('status', ['pending', 'confirmed'])->count(),
            'total_revenue' => $ordersQuery->clone()->where('payment_status', 'paid')->sum('total_amount'),
            'avg_rating' => round($chef->chefProfile?->rating ?? 0, 1),
            'total_menus' => $chef->menus()->count(),
            'active_menus' => $chef->menus()->where('is_available', true)->count(),
        ];

        // 3. Recent Orders
        $recentOrders = Order::where('chef_id', $chef->id)
            ->with(['customer', 'items'])
            ->latest()
            ->take(5)
            ->get();

        // 4. Popular Menus
        $popularMenus = Menu::where('chef_id', $chef->id)
            ->orderByDesc('order_count')
            ->take(5)
            ->get();

        return view('chefs.dashboard', compact('chef', 'stats', 'recentOrders', 'popularMenus'));
    }

    // --- ADMIN DASHBOARD ---
    private function adminDashboard()
    {
        // You can simply redirect to the dedicated Admin Controller
        return redirect()->route('admin.users');
        // Or implement similar logic here if you have a specific dashboard view
        // return view('admin.dashboard');
    }

    // --- CUSTOMER DASHBOARD ---
    private function customerDashboard()
    {
        $user = Auth::user();

        // 1. Calculate Stats
        $stats = [
            'total_orders' => Order::where('customer_id', $user->id)->count(),
            'total_spent' => Order::where('customer_id', $user->id)
                ->whereIn('payment_status', ['paid', 'completed'])
                ->sum('total_amount'),
            'favorite_chefs' => $user->favorites()->where('favoritable_type', 'App\Models\ChefProfile')->count(),
            'loyalty_points' => 0,
        ];

        // 2. Fetch Recent Orders
        $recentOrders = Order::where('customer_id', $user->id)
            ->with(['chef.chefProfile', 'items'])
            ->latest()
            ->take(5)
            ->get();

        // FIXED: Passing data to the view
        return view('customers.dashboard', compact('user', 'stats', 'recentOrders'));
    }
}
