<?php

namespace App\Http\Controllers\Chef;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; // <--- Critical for file operations
use Illuminate\Support\Str; 
use App\Models\ChefProfile; 

class ChefProfileController extends Controller
{
    /**
     * Display the Chef's Profile.
     */
    public function show()
    {
        $user = Auth::user();
        $this->ensureProfileExists($user); // Helper method to clean up code

        return view('chef.profile.show', ['profile' => $user->chefProfile]);
    }

    /**
     * Show the edit form.
     */
    public function edit()
    {
        $user = Auth::user();
        $this->ensureProfileExists($user);

        return view('chef.profile.edit', ['profile' => $user->chefProfile]);
    }

    /**
     * Update the profile in the database.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $this->ensureProfileExists($user);
        $profile = $user->chefProfile;

        // 1. Validation
        $request->validate([
            'business_name' => 'required|string|max:255',
            'delivery_fee' => 'required|numeric|min:0',
            'kitchen_address' => 'required|string|max:255',
            'city' => 'required|string|max:100', // Added City
            'bio' => 'nullable|string|max:1000',
            'operating_hours' => 'nullable|array', 
            'cuisines' => 'nullable|array',
            'years_of_experience' => 'nullable|integer|min:0',
            'minimum_order' => 'nullable|numeric|min:0',
            'bank_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:20',
            'account_name' => 'nullable|string|max:255',
            'cover_image' => 'nullable|image|max:4096', // 4MB Max
            'profile_image' => 'nullable|image|max:2048', // 2MB Max
        ]);

        // 2. Assign Basic Fields
        $profile->business_name = $request->business_name;
        $profile->delivery_fee = $request->delivery_fee;
        $profile->kitchen_address = $request->kitchen_address;
        $profile->city = $request->city; // Save City
        $profile->bio = $request->bio;
        $profile->years_of_experience = $request->years_of_experience;
        $profile->minimum_order = $request->minimum_order;
        $profile->is_online = $request->has('is_online');

        // 3. Bank Details
        $profile->bank_name = $request->bank_name;
        $profile->account_number = $request->account_number;
        $profile->account_name = $request->account_name;
        
        // 4. Array Fields (Casting handled by Model)
        $profile->operating_hours = $request->input('operating_hours');
        $profile->cuisines = $request->input('cuisines');

        // 5. Slug Logic (Update only if name changed)
        if ($profile->isDirty('business_name')) {
            $profile->slug = Str::slug($request->business_name . '-' . $user->id);
        }

        // 6. IMAGE HANDLING: COVER
        if ($request->hasFile('cover_image')) {
            // Delete old
            if ($profile->cover_image) {
                Storage::disk('public')->delete($profile->cover_image);
            }
            // Save new
            $profile->cover_image = $request->file('cover_image')->store('chef_covers', 'public');
        }

        // 7. IMAGE HANDLING: PROFILE/LOGO
        if ($request->hasFile('profile_image')) {
            // Delete old
            if ($profile->profile_image) {
                Storage::disk('public')->delete($profile->profile_image);
            }
            // Save new
            $profile->profile_image = $request->file('profile_image')->store('chef_avatars', 'public');
        }

        $profile->save();

        return redirect()->route('chef.profile')->with('success', 'Kitchen profile updated successfully!');
    }

    /**
     * Show the Personal Info form.
     */
    public function editPersonal()
    {
        return view('chef.profile.personal', ['user' => Auth::user()]);
    }

    /**
     * Update the Personal Info.
     */
    public function updatePersonal(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->phone = $request->phone;

        if ($request->filled('password')) {
            $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
        }

        $user->save();

        return back()->with('success', 'Personal details updated successfully!');
    }

    /**
     * Helper: Ensure a profile exists for the user.
     */
    private function ensureProfileExists($user)
    {
        if (!$user->chefProfile) {
            $businessName = $user->first_name . "'s Kitchen";
            ChefProfile::create([
                'user_id' => $user->id,
                'business_name' => $businessName,
                'slug' => Str::slug($businessName . '-' . $user->id),
                'bio' => 'Welcome to my kitchen!',
                'delivery_fee' => 0,
                'kitchen_address' => 'Update your address',
                'is_online' => true,
                'years_of_experience' => 0
            ]);
            $user->refresh(); // Reload user to get the relation
        }
    }
}