<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Order;
use App\Models\Withdrawal;
use App\Models\Newsletter;
use App\Models\ContactSubmission;
use App\Models\ChefProfile;

class AdminController extends Controller
{
    /**
     * Display a list of Customers with search and filters.
     */
    public function users(Request $request)
    {
        $query = User::role('customer');
        
        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Stats
        $stats = [
            'total' => User::role('customer')->count(),
            'active' => User::role('customer')->where('status', 'active')->orWhereNull('status')->count(),
            'blocked' => User::role('customer')->where('status', 'blocked')->count(),
            'new_this_month' => User::role('customer')->whereMonth('created_at', now()->month)->count(),
        ];
        
        $users = $query->withCount('orders')->latest()->paginate(15)->withQueryString();
        
        return view('admin.users.index', compact('users', 'stats'));
    }

    /**
     * Display a list of Newsletter subscribers.
     */
    public function newsletters()
    {
        $subscribers = Newsletter::latest()->paginate(25);
        return view('admin.newsletters.index', compact('subscribers'));
    }

    /**
     * Delete a newsletter subscriber.
     */
    public function deleteNewsletter($id)
    {
        $subscriber = Newsletter::findOrFail($id);
        $subscriber->delete();
        return back()->with('success', 'Subscriber removed.');
    }

    /**
     * Export newsletter subscribers to CSV.
     */
    public function exportNewsletters()
    {
        $subscribers = Newsletter::orderBy('created_at', 'desc')->get();
        
        $filename = 'newsletter_subscribers_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        return response()->streamDownload(function () use ($subscribers) {
            $handle = fopen('php://output', 'w');
            
            // Add header
            fputcsv($handle, ['Email', 'Subscribed At']);
            
            // Add rows
            foreach ($subscribers as $subscriber) {
                fputcsv($handle, [
                    $subscriber->email,
                    $subscriber->created_at->toDateTimeString()
                ]);
            }
            
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    }

    /**
     * Display contact submissions.
     */
    public function contactSubmissions()
    {
        $submissions = ContactSubmission::latest()->paginate(15);
        return view('admin.contact-submissions.index', compact('submissions'));
    }

    /**
     * Mark contact submission as read.
     */
    public function markContactRead($id)
    {
        $submission = ContactSubmission::findOrFail($id);
        $submission->update(['status' => 'read']);
        return back()->with('success', 'Marked as read.');
    }

    /**
     * Mark contact submission as resolved.
     */
    public function resolveContact($id)
    {
        $submission = ContactSubmission::findOrFail($id);
        $submission->update(['status' => 'resolved']);
        return back()->with('success', 'Marked as resolved.');
    }

    /**
     * Delete contact submission.
     */
    public function deleteContact($id)
    {
        $submission = ContactSubmission::findOrFail($id);
        $submission->delete();
        return back()->with('success', 'Submission deleted.');
    }

    /**
     * Display a list of Chefs with search and filters.
     */
    public function chef(Request $request)
    {
        $query = User::role('chef')->with('chefProfile');
        
        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('chefProfile', function($q2) use ($search) {
                      $q2->where('business_name', 'like', "%{$search}%");
                  });
            });
        }
        
        // Filter by verification status
        if ($request->filled('verification')) {
            if ($request->verification === 'verified') {
                $query->whereHas('chefProfile', fn($q) => $q->where('is_verified', true));
            } elseif ($request->verification === 'pending') {
                $query->whereHas('chefProfile', fn($q) => $q->where('is_verified', false));
            }
        }
        
        // Stats
        $stats = [
            'total' => User::role('chef')->count(),
            'verified' => ChefProfile::where('is_verified', true)->count(),
            'pending' => ChefProfile::where('is_verified', false)->count(),
            'total_revenue' => Order::where('payment_status', 'paid')->sum('total_amount'),
        ];
        
        $chefs = $query->withSum(['chefOrders' => fn($q) => $q->where('payment_status', 'paid')], 'total_amount')
                       ->withCount('chefOrders')
                       ->latest()
                       ->paginate(15)
                       ->withQueryString();
        
        return view('admin.chefs.index', compact('chefs', 'stats'));
    }

    /**
     * Display a list of all Orders with search and filters.
     */
    public function orders(Request $request)
    {
        $query = Order::with(['user', 'chef.chefProfile', 'items']);
        
        // Search by order number or customer
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q2) use ($search) {
                      $q2->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by payment status
        if ($request->filled('payment')) {
            $query->where('payment_status', $request->payment);
        }
        
        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Stats
        $stats = [
            'total' => Order::count(),
            'pending' => Order::whereIn('status', ['pending', 'pending_payment'])->count(),
            'preparing' => Order::where('status', 'preparing')->count(),
            'completed' => Order::where('status', 'completed')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
            'total_revenue' => Order::where('payment_status', 'paid')->sum('total_amount'),
            'today_orders' => Order::whereDate('created_at', today())->count(),
            'today_revenue' => Order::whereDate('created_at', today())->where('payment_status', 'paid')->sum('total_amount'),
        ];
        
        $orders = $query->latest()->paginate(15)->withQueryString();
        
        return view('admin.orders.index', compact('orders', 'stats'));
    }

    /**
     * Display System Reports (Analytics).
     * 
     */
    public function reports(Request $request)
    {
        // 1. DATE FILTERING LOGIC
        // Default to "This Month" if no dates provided
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());

        // 2. KEY METRICS (Filtered by Date)
        $periodOrders = Order::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                             ->where('payment_status', 'paid');

        $totalRevenue = $periodOrders->sum('total_amount');
        $totalOrdersCount = $periodOrders->count();
        $averageOrderValue = $totalOrdersCount > 0 ? $totalRevenue / $totalOrdersCount : 0;
        $platformProfit = $totalRevenue * 0.05; // 5% Fee

        // 3. CHART DATA: Sales Volume per Day (for the selected period)
        $salesChartData = Order::selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where('payment_status', 'paid')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // 4. TOP PERFORMING CHEFS (by Revenue in period)
        $topChefs = User::role('chef')
            ->with('chefProfile')
            ->withSum(['chefOrders' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate])
                      ->where('payment_status', 'paid');
            }], 'total_amount')
            ->get()
            ->sortByDesc('chef_orders_sum_total_amount')
            ->take(5);

        // 5. TOP SELLING DISHES (New Insight!)
        $topDishes = \App\Models\OrderItem::select('menu_name', \DB::raw('SUM(quantity) as total_qty'))
            ->whereHas('order', function($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate])
                  ->where('payment_status', 'paid');
            })
            ->groupBy('menu_name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        return view('admin.reports.index', compact(
            'startDate', 'endDate', 
            'totalRevenue', 'totalOrdersCount', 'averageOrderValue', 'platformProfit',
            'salesChartData', 'topChefs', 'topDishes'
        ));
    }

    /**
     * Display System Settings.
     */
    public function settings()
    {
        // Fetch all settings as key-value pairs
        $settings = DB::table('system_settings')->pluck('value', 'key');
        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update System Settings.
     */
    public function updateSettings(Request $request)
    {
        // Save each input field to the database
        $inputs = $request->except('_token');

        foreach ($inputs as $key => $value) {
            DB::table('system_settings')->updateOrInsert(
                ['key' => $key],
                ['value' => $value, 'updated_at' => now()]
            );
        }

        return back()->with('success', 'System settings updated successfully.');
    }

    // ==========================================
    //    ACTION METHODS (The "Do" Logic)
    // ==========================================

    /**
     * Toggle User Status (Block / Unblock).
     */
    public function toggleUserStatus($id)
    {
        $user = User::findOrFail($id);
        
        // Security: Prevent blocking an Admin
        if ($user->hasRole('admin')) {
            return back()->with('error', 'You cannot block an administrator.');
        }

        // Toggle Logic
        $user->status = ($user->status === 'active') ? 'blocked' : 'active';
        $user->save();

        $message = ($user->status === 'active') ? 'User activated successfully.' : 'User has been blocked.';
        return back()->with('success', $message);
    }

    /**
     * Verify a Chef's Kitchen.
     */
    public function verifyChef($id)
    {
        $chef = User::findOrFail($id);
        
        if ($chef->chefProfile) {
            $chef->chefProfile->update(['is_verified' => true]);
            return back()->with('success', 'Kitchen verified! Trust badge awarded.');
        }

        return back()->with('error', 'Chef profile not found.');
    }

    /**
     * View Full Order Details.
     */
    public function showOrder($id)
    {
        $order = Order::with(['items', 'user', 'chef.chefProfile', 'transaction'])->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }
}