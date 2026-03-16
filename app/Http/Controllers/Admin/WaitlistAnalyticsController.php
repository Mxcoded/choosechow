<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WaitlistSignup;
use App\Models\WaitlistSurvey;
use App\Models\Neighborhood;
use App\Models\ActorCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class WaitlistAnalyticsController extends Controller
{
    /**
     * Main analytics dashboard
     */
    public function dashboard()
    {
        // ============ OVERVIEW STATS ============
        $totalSignups = WaitlistSignup::count();
        $foodLovers = WaitlistSignup::foodLovers()->count();
        $vendors = WaitlistSignup::vendors()->count();
        $surveysCompleted = WaitlistSignup::withSurvey()->count();
        $surveyCompletionRate = $totalSignups > 0 
            ? round(($surveysCompleted / $totalSignups) * 100, 1) 
            : 0;

        // ============ DEMAND VS SUPPLY ============
        $demandSupplyRatio = $vendors > 0 
            ? round($foodLovers / $vendors, 1) 
            : $foodLovers;

        // ============ SIGNUPS BY NEIGHBORHOOD ============
        $signupsByNeighborhood = WaitlistSignup::select('neighborhood_id', DB::raw('count(*) as total'))
            ->with('neighborhood')
            ->groupBy('neighborhood_id')
            ->orderByDesc('total')
            ->limit(15)
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->neighborhood?->name ?? 'Unknown',
                    'total' => $item->total,
                    'food_lovers' => WaitlistSignup::where('neighborhood_id', $item->neighborhood_id)
                        ->foodLovers()->count(),
                    'vendors' => WaitlistSignup::where('neighborhood_id', $item->neighborhood_id)
                        ->vendors()->count(),
                ];
            });

        // ============ VENDOR BREAKDOWN BY CATEGORY ============
        $vendorsByCategory = WaitlistSignup::vendors()
            ->select('actor_category_id', DB::raw('count(*) as total'))
            ->with('actorCategory')
            ->groupBy('actor_category_id')
            ->orderByDesc('total')
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->actorCategory?->name ?? 'Unknown',
                    'total' => $item->total,
                    'icon' => $item->actorCategory?->icon ?? 'fa-store',
                ];
            });

        // ============ TRAFFIC SOURCES ============
        $utmSources = WaitlistSignup::whereNotNull('utm_source')
            ->select('utm_source', DB::raw('count(*) as total'))
            ->groupBy('utm_source')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $discoverySources = WaitlistSignup::whereNull('utm_source')
            ->whereNotNull('discovery_source')
            ->select('discovery_source', DB::raw('count(*) as total'))
            ->groupBy('discovery_source')
            ->orderByDesc('total')
            ->get();

        // ============ REFERRAL ANALYTICS ============
        $topReferrers = WaitlistSignup::withCount('referrals')
            ->having('referrals_count', '>', 0)
            ->orderByDesc('referrals_count')
            ->limit(10)
            ->get();

        $totalReferrals = WaitlistSignup::whereNotNull('referred_by_id')->count();
        
        // Viral coefficient: avg referrals per user
        $viralCoefficient = $totalSignups > 0 
            ? round($totalReferrals / $totalSignups, 2) 
            : 0;

        // ============ DAILY SIGNUPS (Last 30 days) ============
        $dailySignups = WaitlistSignup::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as total'),
                DB::raw('SUM(CASE WHEN role = "food_lover" THEN 1 ELSE 0 END) as food_lovers'),
                DB::raw('SUM(CASE WHEN role = "vendor" THEN 1 ELSE 0 END) as vendors')
            )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // ============ POPULAR MEALS (from surveys) ============
        $popularMeals = $this->getPopularMeals();

        // ============ RECENT SIGNUPS ============
        $recentSignups = WaitlistSignup::with(['neighborhood', 'actorCategory'])
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.waitlist.dashboard', compact(
            'totalSignups',
            'foodLovers',
            'vendors',
            'surveysCompleted',
            'surveyCompletionRate',
            'demandSupplyRatio',
            'signupsByNeighborhood',
            'vendorsByCategory',
            'utmSources',
            'discoverySources',
            'topReferrers',
            'totalReferrals',
            'viralCoefficient',
            'dailySignups',
            'popularMeals',
            'recentSignups'
        ));
    }

    /**
     * Export signups as CSV
     */
    public function export(Request $request): StreamedResponse
    {
        $filename = 'waitlist_signups_' . date('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');

            // Header row
            fputcsv($handle, [
                'ID',
                'Name',
                'Email',
                'Phone',
                'Role',
                'Neighborhood',
                'Vendor Type',
                'UTM Source',
                'UTM Medium',
                'UTM Campaign',
                'Discovery Source',
                'Referrer',
                'Referral Count',
                'Survey Completed',
                'Favorite Meals',
                'Dietary Preferences',
                'Price Range',
                'Created At',
            ]);

            // Data rows
            WaitlistSignup::with(['neighborhood', 'actorCategory', 'referrer', 'survey'])
                ->chunk(500, function ($signups) use ($handle) {
                    foreach ($signups as $signup) {
                        fputcsv($handle, [
                            $signup->id,
                            $signup->name,
                            $signup->email,
                            $signup->phone,
                            $signup->role_display,
                            $signup->neighborhood?->name,
                            $signup->actorCategory?->name,
                            $signup->utm_source,
                            $signup->utm_medium,
                            $signup->utm_campaign,
                            $signup->discovery_source,
                            $signup->referrer?->name,
                            $signup->referral_count,
                            $signup->hasSurvey() ? 'Yes' : 'No',
                            $signup->survey?->favorite_meals_list,
                            $signup->survey?->dietary_preferences_list,
                            $signup->survey?->preferred_price_range,
                            $signup->created_at->format('Y-m-d H:i:s'),
                        ]);
                    }
                });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    /**
     * Get popular meals from surveys
     */
    private function getPopularMeals(): array
    {
        $mealCounts = [];

        WaitlistSurvey::whereNotNull('favorite_meals')
            ->chunk(200, function ($surveys) use (&$mealCounts) {
                foreach ($surveys as $survey) {
                    if (is_array($survey->favorite_meals)) {
                        foreach ($survey->favorite_meals as $meal) {
                            $meal = trim(strtolower($meal));
                            if (!empty($meal)) {
                                $mealCounts[$meal] = ($mealCounts[$meal] ?? 0) + 1;
                            }
                        }
                    }
                }
            });

        arsort($mealCounts);
        return array_slice($mealCounts, 0, 10, true);
    }
}
