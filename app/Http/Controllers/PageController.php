<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ChefProfile;
use App\Models\Review;
use App\Models\Menu;
use App\Models\Newsletter;
use App\Models\ContactSubmission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail; // Added for Contact Form
use Illuminate\Support\Facades\Log;  // Added for Logging

class PageController extends Controller
{
    /**
     * LANDING PAGE
     */
    public function landingPage()
    {
        // 1. STATS (Now using the shared helper)
        $stats = $this->getStats();

        // 2. FEATURED CHEFS (Optimized)
        $featuredChefs = ChefProfile::where('is_online', true)
            ->with(['user.receivedReviews'])
            ->inRandomOrder()
            ->take(4)
            ->get();

        // 3. POPULAR CUISINES (Based on Chef Tags)
        $popularCuisines = collect();
        $profiles = ChefProfile::where('is_online', true)->get();
        $cuisineCounts = [];

        foreach ($profiles as $profile) {
            if (!empty($profile->cuisines) && is_array($profile->cuisines)) {
                foreach ($profile->cuisines as $tag) {
                    $tag = trim($tag);
                    if (!empty($tag)) {
                        if (!isset($cuisineCounts[$tag])) {
                            $cuisineCounts[$tag] = 0;
                        }
                        $cuisineCounts[$tag]++;
                    }
                }
            }
        }
        arsort($cuisineCounts);
        foreach (array_slice($cuisineCounts, 0, 6) as $name => $count) {
            $popularCuisines->push((object)[
                'category' => $name,
                'total'    => $count
            ]);
        }

        // 4. SEARCH SUGGESTIONS
        $cityNames = ChefProfile::whereNotNull('city')->distinct()->pluck('city')->filter();
        $foodNames = Menu::distinct()->take(20)->pluck('name')->filter();
        $searchSuggestions = $cityNames->merge($foodNames)->unique()->values();

        return view('welcome', compact('stats', 'featuredChefs', 'popularCuisines', 'searchSuggestions'));
    }

    /**
     * ABOUT PAGE (Now Dynamic)
     */
    public function about()
    {
        $stats = $this->getStats();
        // FIX: Point to 'pages.about'
        return view('pages.about', compact('stats'));
    }

    public function contact()
    {
        // FIX: Point to 'pages.contact'
        return view('pages.contact');
    }
    /**
     * HANDLE CONTACT FORM SUBMISSION
     */
    public function submitContact(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        try {
            // Save to database
            ContactSubmission::create([
                'name' => $request->name,
                'email' => $request->email,
                'subject' => $request->subject,
                'message' => $request->message,
                'status' => 'new'
            ]);

            // Send email to Admin
            Mail::raw($request->message, function ($msg) use ($request) {
                $msg->to('support@choosechow.com')
                    ->subject('Contact Form: ' . $request->subject)
                    ->replyTo($request->email, $request->name);
            });
            
            return back()->with('success', 'Message sent! We will get back to you shortly.');

        } catch (\Exception $e) {
            // Log the error but show a user-friendly message
            Log::error('Contact Form Error: ' . $e->getMessage());
            return back()->with('error', 'Unable to send message. Please try again later.');
        }
    }

    /**
     * HANDLE NEWSLETTER SUBSCRIPTION
     */
    public function subscribe(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        try {
            // Check if email already subscribed
            $exists = Newsletter::where('email', $request->email)->exists();
            
            if ($exists) {
                return back()->with('error', 'This email is already subscribed to our newsletter.');
            }

            // Save email to database
            Newsletter::create([
                'email' => $request->email
            ]);

            // Log the subscription
            Log::info('Newsletter Subscription', [
                'email' => $request->email,
                'subscribed_at' => now()
            ]);
            
            return back()->with('success', 'You have successfully subscribed to our newsletter!');
        } catch (\Exception $e) {
            // Log the error
            Log::error('Newsletter Subscription Error: ' . $e->getMessage());
            return back()->with('error', 'Unable to subscribe. Please try again later.');
        }
    }

    /**
     * STATIC PAGES
     */
    public function howItWorks()
    {
        return view('pages.how-it-works');
    }

    public function privacy()
    {
        return view('pages.privacy');
    }

    public function terms()
    {
        return view('pages.terms');
    }

    /**
     * HELPER: GET SYSTEM STATS
     * (Shared between Landing and About pages)
     */
    private function getStats()
    {
        return [
            'verified_chefs'  => ChefProfile::where('is_online', true)->count(),
            'happy_customers' => User::role('customer')->count(),
            'cities_covered'  => ChefProfile::whereNotNull('city')
                                    ->where('city', '!=', '')
                                    ->distinct('city')
                                    ->count('city'),
            'average_rating'  => number_format(Review::avg('rating') ?: 5.0, 1),
        ];
    }
}