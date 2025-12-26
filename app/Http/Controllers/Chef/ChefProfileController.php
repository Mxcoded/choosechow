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

    public function show()
    {
        $chef = Auth::user();
        $profile = $chef->chefProfile;

        if (!$profile) {
            return redirect()->route('chef.profile.edit')->with('info', 'Please complete your profile.');
        }
        return view('chefs.profile.show', compact('chef', 'profile'));
    }

    public function edit()
    {
        $chef = Auth::user();
        $profile = $chef->chefProfile ?? new ChefProfile();
        $cuisines = Cuisine::orderBy('name')->get();

        // Initialize default hours if empty
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

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            // User Basic
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'avatar' => 'nullable|image|max:2048',

            // Profile
            'business_name' => 'required|string|max:255',
            'bio' => 'required|string|max:1000',
            'kitchen_address' => 'required|string|max:255',
            'years_of_experience' => 'required|integer|min:0',
            'minimum_order' => 'required|numeric|min:0',

            // Settings
            'operating_hours' => 'required|array',
            'cuisine_ids' => 'required|array|min:1',
            'is_online' => 'nullable', // Checkbox sends '1' or nothing

            // Bank
            'bank_name' => 'nullable|string',
            'account_number' => 'nullable|string',
            'account_name' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            // 1. Update User
            if ($request->hasFile('avatar')) {
                $user->avatar = $request->file('avatar')->store('avatars', 'public');
            }
            $user->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'phone' => $request->phone,
            ]);

            // 2. Update Profile
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

            // Handle Checkbox & Slug
            $profileData['is_online'] = $request->has('is_online');
            $profileData['slug'] = Str::slug($request->business_name);

            $profile = $user->chefProfile()->updateOrCreate(
                ['user_id' => $user->id],
                $profileData
            );

            // 3. Relationships
            $profile->cuisines()->sync($request->cuisine_ids);

            DB::commit();
            return redirect()->route('chef.profile')->with('success', 'Profile updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Update failed: ' . $e->getMessage())->withInput();
        }
    }
}
