<?php

namespace App\Http\Controllers\Chef;

use App\Http\Controllers\Controller;
use App\Models\ChefProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class ChefProfileController extends Controller
{
    public function __construct()
    {
        // Ensure the user is authenticated and is a chef
        $this->middleware(['auth', 'chef']);
    }

    /**
     * Display the Chef's profile (Show).
     */
    public function show()
    {
        $chef = Auth::user();
        $profile = $chef->chefProfile;

        // If a profile doesn't exist (e.g., first login), redirect to create/edit
        if (!$profile) {
            return redirect()->route('chef.profile.edit')->with('warning', 'Please complete your chef profile first.');
        }

        return view('chefs.profile.show', compact('chef', 'profile'));
    }

    /**
     * Show the form for editing the Chef's profile.
     */
    public function edit()
    {
        $chef = Auth::user();
        $profile = $chef->chefProfile ?? new ChefProfile(['user_id' => $chef->id]);

        // Use a generic model to avoid errors if the profile hasn't been created yet
        return view('chefs.profile.edit', compact('chef', 'profile'));
    }

    /**
     * Store a newly created profile or update an existing one.
     */
    public function update(Request $request)
    {
        $chef = Auth::user();
        $profile = $chef->chefProfile;
        $isCreating = !$profile;

        $validated = $request->validate($this->getValidationRules($isCreating));

        // --- FIX APPLIED HERE: Handle the boolean switch for accepts_orders ---
        // If the checkbox is present, use its value (1). If not, set it to 0 (false).
        $validated['accepts_orders'] = $request->has('accepts_orders');
        // -------------------------------------------------------------------

        // Process array fields (cuisines, specialties)
        $validated = $this->processArrayFields($validated);

        // Process complex fields (operating hours)
        $validated = $this->processOperatingHours($validated, $request);

        // Handle profile creation vs. update
        if ($isCreating) {
            $validated['user_id'] = $chef->id;
            $validated['slug'] = $this->generateSlug($validated['business_name']);
            ChefProfile::create($validated);
            $message = 'Profile created successfully! You can now add menu items.';
        } else {
            // Update slug only if the business name changed
            if (isset($validated['business_name']) && $validated['business_name'] !== $profile->business_name) {
                $validated['slug'] = $this->generateSlug($validated['business_name']);
            }
            $profile->update($validated);
            $message = 'Profile updated successfully!';
        }

        return redirect()->route('chef.profile')->with('success', $message);
    }

    /* Private Methods for Data Processing and Validation */

    private function getValidationRules(bool $isCreating): array
    {
        $chefId = Auth::id();

        return [
            'business_name' => [
                'required',
                'string',
                'max:255',
                // Unique slug rule, ignoring the current profile's slug during update
                Rule::unique('chef_profiles', 'slug')->ignore($isCreating ? null : $chefId, 'user_id'),
            ],
            'bio' => 'required|string|max:1000',
            'specialties' => 'required|string|max:500',
            'years_of_experience' => 'required|integer|min:0|max:80',
            'cuisines' => 'required|string|max:500', // Still comma-separated input
            'kitchen_address' => 'required|string|max:500',
            'kitchen_latitude' => 'nullable|numeric|between:-90,90',
            'kitchen_longitude' => 'nullable|numeric|between:-180,180',
            'delivery_radius_km' => 'required|integer|min:1|max:500',
            'minimum_order_amount' => 'required|numeric|min:0',
            'delivery_fee' => 'required|numeric|min:0',
            'free_delivery_over_amount' => 'boolean',
            'free_delivery_threshold' => 'nullable|numeric|min:0|required_if:free_delivery_over_amount,1',
            'accepts_orders' => 'boolean',

            // Banking (Can be required later, but nullable for initial setup)
            'bank_name' => 'nullable|string|max:100',
            'account_number' => 'nullable|string|max:20',
            'account_name' => 'nullable|string|max:255',
            'bvn' => 'nullable|string|min:11|max:11',

            // Operating Hours (Structured Array Input)
            'operating_hours.*.is_open' => 'nullable|boolean',
            'operating_hours.*.open_time' => 'nullable|date_format:H:i',
            'operating_hours.*.close_time' => 'nullable|date_format:H:i|after:operating_hours.*.open_time',
        ];
    }

    private function processArrayFields(array $validated): array
    {
        $arrayFields = ['cuisines', 'specialties'];

        foreach ($arrayFields as $field) {
            if (isset($validated[$field]) && $validated[$field]) {
                // Convert comma-separated string to array
                $validated[$field] = array_filter(array_map('trim', explode(',', $validated[$field])));
            } else {
                $validated[$field] = [];
            }
        }
        return $validated;
    }

    private function processOperatingHours(array $validated, Request $request): array
    {
        $schedule = [];
        $rawHours = $request->input('operating_hours', []);

        foreach ($rawHours as $day => $data) {
            $schedule[$day] = [
                'is_open' => $data['is_open'] ?? false,
                'open_time' => $data['open_time'] ?? null,
                'close_time' => $data['close_time'] ?? null,
            ];
        }

        $validated['operating_hours'] = $schedule;
        return $validated;
    }

    private function generateSlug(string $businessName): string
    {
        $baseSlug = Str::slug($businessName);
        $slug = $baseSlug;
        $count = 1;

        while (ChefProfile::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $count++;
        }

        return $slug;
    }
}
