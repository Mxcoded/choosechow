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
            'avg_rating' => round($chef->chefProfile?->rating ?? 0, 1), // Use ChefProfile rating
            'total_menus' => $chef->menus()->count(),
            'active_menus' => $chef->menus()->where('is_available', true)->count(),
        ];

        // 3. Recent Orders (Eager Loaded for Performance)
        $recentOrders = Order::where('chef_id', $chef->id)
            ->with(['customer', 'items']) // <--- IMPORTANT: Load items
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

    private function adminDashboard()
    {
        return view('admin.dashboard');
    } // Placeholder
    private function customerDashboard()
    {
        return view('customers.dashboard');
    } // Placeholder
}
