<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\User;
use App\Models\Withdrawal;
use App\Models\ChefProfile;
use App\Models\Menu;
use App\Models\Newsletter;
use App\Models\ContactSubmission;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // ==========================================
        //    1. ADMIN DASHBOARD
        // ==========================================
        if ($user->hasRole('admin')) {
            $totalSales = Order::where('payment_status', 'paid')->sum('total_amount');
            $platformProfit = $totalSales * 0.05; 
            
            $stats = [
                'revenue' => $platformProfit, 
                'total_flow' => $totalSales,
                'total_users' => User::role('customer')->count(),
                'total_chefs' => User::role('chef')->count(),
                'pending_payouts' => Withdrawal::where('status', 'pending')->count(),
                'pending_verifications' => ChefProfile::where('is_verified', false)->count(),
                'newsletter_subscribers' => Newsletter::count(),
                'pending_contacts' => ContactSubmission::where('status', 'new')->count(),
            ];

            $recentOrders = Order::with('user', 'chef')->latest()->take(5)->get();

            return view('admin.dashboard', compact('stats', 'recentOrders'));
        }

        // ==========================================
        //    2. CHEF DASHBOARD
        // ==========================================
        if ($user->hasRole('chef')) {
            $orders = Order::where('chef_id', $user->id);
            
            $totalSales = $orders->where('payment_status', 'paid')->sum('total_amount');
            $earnings = $totalSales * 0.95; 

            $activeOrdersCount = Order::where('chef_id', $user->id)
                ->whereIn('status', ['pending_payment', 'pending', 'preparing', 'ready'])
                ->count();
                
            $completedOrdersCount = Order::where('chef_id', $user->id)
                ->where('status', 'completed')
                ->count();

            $activeMenusCount = Menu::where('user_id', $user->id)
                ->where('is_available', true)
                ->count();

            $recentOrders = Order::where('chef_id', $user->id)
                ->with('user')
                ->latest()
                ->take(5)
                ->get();

            return view('dashboard', compact(
                'earnings', 'activeOrdersCount', 'completedOrdersCount', 'activeMenusCount', 'recentOrders'
            )); 
        }

        // ==========================================
        //    3. CUSTOMER DASHBOARD (UPDATED)
        // ==========================================
        if ($user->hasRole('customer')) {
            
            // 1. Calculate Stats
            $stats = [
                'total_orders' => Order::where('user_id', $user->id)->count(),
                // Sum only paid orders
                'total_spent' => Order::where('user_id', $user->id)
                                      ->where('payment_status', 'paid')
                                      ->sum('total_amount'),
                // Count unique chefs they have ordered from
                'favorite_chefs' => Order::where('user_id', $user->id)
                                         ->distinct('chef_id')
                                         ->count('chef_id')
            ];

            // 2. Get Recent Orders
            $recentOrders = Order::where('user_id', $user->id)
                ->with(['chef.chefProfile', 'items']) // Load chef profile for avatars
                ->latest()
                ->take(5)
                ->get();
            
            // 3. Add Status Colors (Helper logic for the view)
            foreach($recentOrders as $order) {
                $order->status_color = match($order->status) {
                    'pending_payment' => 'warning',
                    'pending' => 'warning',
                    'preparing' => 'info',
                    'ready' => 'info',
                    'completed' => 'success',
                    'cancelled' => 'danger',
                    default => 'secondary'
                };
            }

            return view('customer.dashboard', compact('user', 'stats', 'recentOrders'));
        }

        // Fallback
        return redirect()->route('welcome');
    }
}