<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WaitlistSignup;
use App\Models\WaitlistSurvey;
use App\Models\Neighborhood;
use App\Models\ActorCategory;
use App\Models\Cuisine;
use App\Models\DietaryPreference;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class WaitlistController extends Controller
{
    /**
     * Landing page for waitlist
     */
    public function index(Request $request)
    {
        // Store UTM params and referral in session
        $this->captureTrackingParams($request);
        
        // Stats for landing page
        $stats = [
            'total_signups' => WaitlistSignup::count(),
            'food_lovers' => WaitlistSignup::foodLovers()->count(),
            'vendors' => WaitlistSignup::vendors()->count(),
            'neighborhoods' => WaitlistSignup::distinct('neighborhood_id')->count('neighborhood_id'),
        ];

        return view('waitlist.index', compact('stats'));
    }

    /**
     * Show signup form (Step 1)
     */
    public function create(Request $request)
    {
        // Capture tracking params
        $this->captureTrackingParams($request);
        
        // Get form data
        $neighborhoods = Neighborhood::active()->ordered()->get();
        $actorCategories = ActorCategory::active()->ordered()->get();
        
        // Check for referrer
        $referrer = null;
        $refToken = $request->query('ref') ?? session('ref_token');
        if ($refToken) {
            $referrer = WaitlistSignup::where('referral_token', $refToken)->first();
        }
        
        // Discovery source options (for when no UTM)
        $discoverySources = [
            'social_media' => 'Social Media (Instagram, Twitter, etc.)',
            'friend' => 'Friend or Family',
            'search' => 'Google / Search Engine',
            'advertisement' => 'Advertisement',
            'influencer' => 'Influencer / Content Creator',
            'event' => 'Event / Meetup',
            'other' => 'Other',
        ];
        
        // Check if we have UTM params
        $hasUtm = session('utm_source') || session('utm_medium') || session('utm_campaign');

        return view('waitlist.create', compact(
            'neighborhoods',
            'actorCategories',
            'referrer',
            'discoverySources',
            'hasUtm'
        ));
    }

    /**
     * Process signup (Step 1)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:waitlist_signups,email',
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:food_lover,vendor',
            'neighborhood_id' => 'required|exists:neighborhoods,id',
            'actor_category_id' => 'required_if:role,vendor|nullable|exists:actor_categories,id',
            'discovery_source' => 'nullable|string|max:50',
        ], [
            'email.unique' => 'This email is already on our waitlist!',
            'actor_category_id.required_if' => 'Please select your vendor type.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Find referrer if exists
            $referrerId = null;
            $refToken = $request->input('ref_token') ?? session('ref_token');
            if ($refToken) {
                $referrer = WaitlistSignup::where('referral_token', $refToken)->first();
                $referrerId = $referrer?->id;
            }

            // Create signup
            $signup = WaitlistSignup::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'role' => $request->role,
                'neighborhood_id' => $request->neighborhood_id,
                'actor_category_id' => $request->role === 'vendor' ? $request->actor_category_id : null,
                'referred_by_id' => $referrerId,
                'utm_source' => session('utm_source'),
                'utm_medium' => session('utm_medium'),
                'utm_campaign' => session('utm_campaign'),
                'utm_content' => session('utm_content'),
                'utm_term' => session('utm_term'),
                'discovery_source' => !session('utm_source') ? $request->discovery_source : null,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'step_completed' => 1,
            ]);

            // Clear session tracking data
            $this->clearTrackingSession();

            // Redirect to survey (optional step 2)
            return redirect()->route('waitlist.survey', $signup->referral_token);

        } catch (\Exception $e) {
            Log::error('Waitlist signup error: ' . $e->getMessage());
            return back()->with('error', 'Something went wrong. Please try again.')->withInput();
        }
    }

    /**
     * Show survey form (Step 2 - Optional)
     */
    public function survey(string $token)
    {
        $signup = WaitlistSignup::where('referral_token', $token)->firstOrFail();
        
        // If already completed survey, go to success
        if ($signup->hasSurvey()) {
            return redirect()->route('waitlist.success', $token);
        }

        // Get survey options
        $cuisines = Cuisine::orderBy('name')->pluck('name', 'id');
        $dietaryPreferences = DietaryPreference::orderBy('name')->pluck('name', 'id');
        
        $priceRanges = [
            'budget' => 'Budget (₦1500 - ₦5,000)',
            'mid-range' => 'Mid-Range (₦5,500 - ₦10,000)',
            'premium' => 'Premium (₦10,500+)',
        ];

        return view('waitlist.survey', compact(
            'signup',
            'cuisines',
            'dietaryPreferences',
            'priceRanges'
        ));
    }

    /**
     * Save survey responses (Step 2)
     */
    public function storeSurvey(Request $request, string $token)
    {
        $signup = WaitlistSignup::where('referral_token', $token)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'favorite_meals' => 'nullable|array',
            'favorite_meals.*' => 'string|max:100',
            'dietary_preferences' => 'nullable|array',
            'reason_for_choosing' => 'nullable|string|max:1000',
            'preferred_price_range' => 'nullable|in:budget,mid-range,premium',
            'meals_per_week' => 'nullable|integer|min:1|max:21',
            'preferred_cuisines' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Create or update survey
            WaitlistSurvey::updateOrCreate(
                ['waitlist_signup_id' => $signup->id],
                [
                    'favorite_meals' => $request->favorite_meals,
                    'dietary_preferences' => $request->dietary_preferences,
                    'reason_for_choosing' => $request->reason_for_choosing,
                    'preferred_price_range' => $request->preferred_price_range,
                    'meals_per_week' => $request->meals_per_week,
                    'preferred_cuisines' => $request->preferred_cuisines,
                ]
            );

            // Mark survey completed
            $signup->markSurveyCompleted();

            return redirect()->route('waitlist.success', $token);

        } catch (\Exception $e) {
            Log::error('Waitlist survey error: ' . $e->getMessage());
            return back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    /**
     * Success page with referral link
     */
    public function success(string $token)
    {
        $signup = WaitlistSignup::where('referral_token', $token)->firstOrFail();
        
        // Get waitlist position (approximate)
        $position = WaitlistSignup::where('id', '<=', $signup->id)->count();
        
        // Count referrals
        $referralCount = $signup->referrals()->count();

        return view('waitlist.success', compact('signup', 'position', 'referralCount'));
    }

    /**
     * Skip survey and go to success
     */
    public function skipSurvey(string $token)
    {
        $signup = WaitlistSignup::where('referral_token', $token)->firstOrFail();
        return redirect()->route('waitlist.success', $token);
    }

    // ================== HELPERS ==================

    /**
     * Capture UTM and referral params from URL
     */
    private function captureTrackingParams(Request $request): void
    {
        // Store UTM params
        if ($request->has('utm_source')) {
            session(['utm_source' => $request->query('utm_source')]);
        }
        if ($request->has('utm_medium')) {
            session(['utm_medium' => $request->query('utm_medium')]);
        }
        if ($request->has('utm_campaign')) {
            session(['utm_campaign' => $request->query('utm_campaign')]);
        }
        if ($request->has('utm_content')) {
            session(['utm_content' => $request->query('utm_content')]);
        }
        if ($request->has('utm_term')) {
            session(['utm_term' => $request->query('utm_term')]);
        }
        
        // Store referral token
        if ($request->has('ref')) {
            session(['ref_token' => $request->query('ref')]);
        }
    }

    /**
     * Clear tracking session data after signup
     */
    private function clearTrackingSession(): void
    {
        session()->forget([
            'utm_source',
            'utm_medium',
            'utm_campaign',
            'utm_content',
            'utm_term',
            'ref_token',
        ]);
    }
}
