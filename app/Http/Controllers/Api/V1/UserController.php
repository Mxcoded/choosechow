<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    use ApiResponse;

    /**
     * Get authenticated user profile.
     * 
     * GET /api/v1/user
     */
    public function show(Request $request)
    {
        $user = $request->user()->load([
            'chefProfile.cuisines',
            'addresses',
            'activeSubscription.plan',
        ]);

        return $this->success(new UserResource($user));
    }

    /**
     * Update user profile.
     * 
     * PUT /api/v1/user
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => ['sometimes', 'string', 'max:255'],
            'last_name' => ['sometimes', 'string', 'max:255'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:20'],
            'date_of_birth' => ['sometimes', 'nullable', 'date', 'before:today'],
            'gender' => ['sometimes', 'nullable', 'in:male,female,other'],
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $user = $request->user();
        
        $user->update($request->only([
            'first_name',
            'last_name',
            'phone',
            'date_of_birth',
            'gender',
        ]));

        return $this->success(
            new UserResource($user->fresh()->load('chefProfile')),
            'Profile updated successfully'
        );
    }

    /**
     * Update user avatar.
     * 
     * POST /api/v1/user/avatar
     */
    public function updateAvatar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'avatar' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $user = $request->user();

        // Delete old avatar if exists
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Store new avatar
        $path = $request->file('avatar')->store('avatars', 'public');
        
        $user->update(['avatar' => $path]);

        return $this->success([
            'avatar' => $user->fresh()->avatar_url,
        ], 'Avatar updated successfully');
    }

    /**
     * Update user preferences.
     * 
     * PUT /api/v1/user/preferences
     */
    public function updatePreferences(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'preferences' => ['required', 'array'],
            'preferences.notifications_enabled' => ['sometimes', 'boolean'],
            'preferences.email_notifications' => ['sometimes', 'boolean'],
            'preferences.push_notifications' => ['sometimes', 'boolean'],
            'preferences.sms_notifications' => ['sometimes', 'boolean'],
            'preferences.default_cuisine' => ['sometimes', 'nullable', 'string'],
            'preferences.dietary_preferences' => ['sometimes', 'array'],
            'preferences.language' => ['sometimes', 'string', 'in:en,fr'],
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $user = $request->user();
        
        // Merge with existing preferences
        $currentPrefs = $user->preferences ?? [];
        $newPrefs = array_merge($currentPrefs, $request->preferences);
        
        $user->update(['preferences' => $newPrefs]);

        return $this->success([
            'preferences' => $user->fresh()->preferences,
        ], 'Preferences updated successfully');
    }
}
