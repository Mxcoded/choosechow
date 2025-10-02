<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Menu; // Import the Menu model
// NOTE: Assuming Order and Review models exist for real data
use App\Models\Order; 
// use App\Models\Review;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function index()
    {
        $user = Auth::user();

        if ($user->user_type === 'admin') {
            return $this->adminDashboard();
        } elseif ($user->user_type === 'chef') {
            return $this->chefDashboard();
        } else {
            return $this->customerDashboard();
        }
    }

    private function adminDashboard()
    {
        // ... (Admin dashboard logic remains unchanged for this task) ...
        $user = Auth::user();

        // Admin statistics
        $stats = [
            'total_users' => User::count(),
            'total_chefs' => User::where('user_type', 'chef')->count(),
            'total_customers' => User::where('user_type', 'customer')->count(),
            'new_users_today' => User::whereDate('created_at', today())->count(),
            'total_orders' => 156, // Replace with actual order count
            'total_revenue' => 12450.75, // Replace with actual revenue
            'pending_approvals' => User::where('user_type', 'chef')->where('status', 'pending')->count(),
            'active_chefs' => User::where('user_type', 'chef')->where('status', 'active')->count(),
        ];

        $recentUsers = User::latest()->take(5)->get();
        $recentChefs = User::where('user_type', 'chef')->latest()->take(5)->get();

        return view('admin.dashboard', compact('user', 'stats', 'recentUsers', 'recentChefs'));
    }

    private function chefDashboard()
    {
        $chef = Auth::user();

        // --- BASE QUERIES ---
        $chefMenuQuery = Menu::where('chef_id', $chef->id);
        $chefOrderQuery = Order::where('chef_id', $chef->id)->where('payment_status', 'paid');

        // --- REAL DATA CALCULATIONS ---

        // 1. Order Metrics
        $totalOrders = $chefOrderQuery->count();
        $pendingOrders = $chefOrderQuery->whereIn('status', ['pending', 'confirmed'])->count();
        $totalRevenue = $chefOrderQuery->sum('total_amount');

        // Reset query for total orders for recent data
        $recentOrders = Order::where('chef_id', $chef->id)
            ->where('payment_status', 'paid')
            ->latest()
            ->with('customer')
            ->take(5)
            ->get();

        // 2. Menu Metrics
        $totalMenus = $chefMenuQuery->count();
        $activeMenus = $chefMenuQuery->where('is_available', true)->count();

        // Calculate average rating across all menus
        // Note: It's safer to query the database directly for the average of averages
        $avgRating = round(Menu::where('chef_id', $chef->id)->avg('average_rating') ?? 0, 1);

        // Get popular menus (order count descending)
        $popularMenus = Menu::where('chef_id', $chef->id)
            ->orderByDesc('order_count')
            ->take(5)
            ->get();

        // 3. Customer/Engagement Metrics (Placeholders for now, but structured)
        // NOTE: Total customers requires distinct customer_ids from the Order table
        $totalCustomers = Order::where('chef_id', $chef->id)
            ->where('payment_status', 'paid')
            ->distinct('customer_id')
            ->count('customer_id');

        $stats = [
            'total_orders' => $totalOrders,
            'pending_orders' => $pendingOrders,
            'total_revenue' => $totalRevenue,

            // Menu Stats
            'total_menus' => $totalMenus,
            'active_menus' => $activeMenus,
            'avg_rating' => $avgRating,

            // Engagement
            'total_customers' => $totalCustomers,
            'order_completion_rate' => 0.94, // Placeholder for actual calculation
        ];

        return view('chefs.dashboard', compact('chef', 'stats', 'recentOrders', 'popularMenus'));
    }

    private function customerDashboard()
    {
        // ... (Customer dashboard logic remains unchanged for this task) ...
        $user = Auth::user();

        // Mock data for now - replace with real queries later
        $stats = [
            'total_orders' => 12,
            'favorite_chefs' => 5,
            'total_spent' => 485.50,
            'loyalty_points' => 120
        ];

        $recentOrders = []; // Will be populated from database later
        $favoriteChefs = []; // Will be populated from database later
        $recommendedMeals = []; // Will be populated from database later

        return view('customers.dashboard', compact('user', 'stats', 'recentOrders', 'favoriteChefs', 'recommendedMeals'));
    }
}
