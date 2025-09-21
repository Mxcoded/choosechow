<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function index()
    {
        $user = Auth::user();

        if ($user->user_type === 'chef') {
            return $this->chefDashboard();
        } else {
            return $this->customerDashboard();
        }
    }

    private function chefDashboard()
    {
        $user = Auth::user();

        // Mock data for now - replace with real queries later
        $stats = [
            'total_orders' => 45,
            'pending_orders' => 3,
            'total_revenue' => 2850.00,
            'avg_rating' => 4.8,
            'active_menus' => 8,
            'total_customers' => 32
        ];

        $recentOrders = []; // Will be populated from database later
        $popularMenus = []; // Will be populated from database later

        return view('chefs.dashboard', compact('user', 'stats', 'recentOrders', 'popularMenus'));
    }

    private function customerDashboard()
    {
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
