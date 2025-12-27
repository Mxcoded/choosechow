<?php

namespace App\Http\Controllers\Chef;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\ChefProfile;
use App\Models\Cuisine;
use Illuminate\Support\Str;

class ChefProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:chef']);
    }

    /**
     * Display the chef's profile.
     */
    public function show()
    {
        $chef = Auth::user();
        $profile = $chef->chefProfile;

        // Redirect to edit if profile is incomplete
        if (!$profile) {
            return redirect()->route('chef.profile.edit')
                ->with('info', 'Please complete your profile setup to start receiving orders.');
        }

        // Load relationships for the view
        $profile->load(['cuisines']);

        return view('chefs.profile.show', compact('chef', 'profile'));
    }

    /**
     * Show the form for editing the profile.
     */
    public function edit()
    {
        $chef = Auth::user();
        // Get existing profile or create a new instance (in memory)
        $profile = $chef->chefProfile ?? new ChefProfile();

        // Fetch all cuisines for the checklist
        $cuisines = Cuisine::orderBy('name')->get();

        // Initialize default operating hours if specific data is missing
        if (empty($profile->operating_hours)) {
            $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
            $defaultHours = [];
            foreach ($days as $day) {
                $defaultHours[$day] = ['open' => '09:00', 'close' => '20:00', 'closed' => false];
            }
            $profile->operating_hours = $defaultHours;
        }

        return view('chefs.profile.edit', compact('chef', 'profile', 'cuisines'));
    }

    /**
     * Update or Create the chef's profile.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // 1. Validation
        $validated = $request->validate([
            // User Account Info
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'avatar' => 'nullable|image|max:2048',

            // Business Profile Info
            'business_name' => 'required|string|max:255',
            'bio' => 'required|string|max:1000',
            'kitchen_address' => 'required|string|max:255', // Required by DB
            'years_of_experience' => 'required|integer|min:0',

            // Store Settings
            'minimum_order' => 'required|numeric|min:0',
            'operating_hours' => 'required|array',
            'cuisine_ids' => 'required|array|min:1', // Must select at least 1 cuisine
            'cuisine_ids.*' => 'exists:cuisines,id',
            'is_online' => 'nullable', // Checkbox sends '1' or null

            // Financial Info
            'bank_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:20',
            'account_name' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            // 2. Update User Table (Base Info)
            if ($request->hasFile('avatar')) {
                // Delete old avatar if exists (optional logic)
                $user->avatar = $request->file('avatar')->store('avatars', 'public');
            }

            $user->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'phone' => $request->phone,
            ]);

            // 3. Prepare Profile Data
            $profileData = $request->only([
                'business_name',
                'bio',
                'kitchen_address',
                'years_of_experience',
                'minimum_order',
                'operating_hours',
                'bank_name',
                'account_number',
                'account_name'
            ]);

            // Explicitly handle fields that might need transformation
            $profileData['is_online'] = $request->has('is_online'); // Convert checkbox to boolean

            // Only generate slug if we are creating, or if you want to allow slug updates:
            // $profileData['slug'] = Str::slug($request->business_name); 
            // (Note: The Model boot() method also handles slug generation on save)

            // 4. Update or Create Profile Record
            $profile = $user->chefProfile()->updateOrCreate(
                ['user_id' => $user->id],
                $profileData
            );

            // 5. Sync Relationships (Cuisines)
            $profile->cuisines()->sync($request->cuisine_ids);

            DB::commit();

            return redirect()->route('chef.profile')
                ->with('success', 'Profile updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            // Log the error for debugging: \Log::error($e);
            return back()
                ->with('error', 'Error saving profile: ' . $e->getMessage())
                ->withInput();
        }
    }
    /**
     * Display the chef's dashboard.
     */
    public function dashboard()
    {
        return view('chefs.dashboard');
    }
}
