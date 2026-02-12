<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // <--- Added missing import
use App\Models\User;
use App\Models\Order;
use App\Models\Withdrawal; // <--- Added missing import
use App\Models\Newsletter;
use App\Models\ContactSubmission;

class AdminController extends Controller
{
    /**
     * Display a list of Customers.
     */
    public function users()
    {
        $users = User::role('customer')->latest()->paginate(15);
        return view('admin.users.index', compact('users'));
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
     * Display a list of Chefs.
     */
    public function chef()
    {
        $chefs = User::role('chef')->with('chefProfile')->latest()->paginate(15);
        return view('admin.chefs.index', compact('chefs'));
    }

    /**
     * Display a list of all Orders.
     */
    public function orders()
    {
        $orders = Order::with(['user', 'chef', 'items'])->latest()->paginate(15);
        return view('admin.orders.index', compact('orders'));
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