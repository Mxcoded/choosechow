<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Auth\Events\Registered;

class AuthController extends Controller
{
    use ApiResponse;

    /**
     * Register a new user.
     * 
     * POST /api/v1/auth/register
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'confirmed', PasswordRule::defaults()],
            'role' => ['required', 'in:customer,chef'],
            'device_token' => ['nullable', 'string'], // For push notifications
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => $request->password, // Mutator handles hashing
            'device_token' => $request->device_token,
        ]);

        // Assign role
        $user->assignRole($request->role);

        // Create chef profile if registering as chef
        if ($request->role === 'chef') {
            $user->chefProfile()->create([
                'business_name' => $user->full_name . "'s Kitchen",
            ]);
        }

        // Generate referral code
        $user->generateReferralCode();

        // Fire registered event (for email verification)
        event(new Registered($user));

        // Create API token
        $token = $user->createToken('mobile-app')->plainTextToken;

        return $this->created([
            'user' => new UserResource($user->fresh()->load('chefProfile')),
            'token' => $token,
            'token_type' => 'Bearer',
        ], 'Registration successful. Please verify your email.');
    }

    /**
     * Login user and return token.
     * 
     * POST /api/v1/auth/login
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'device_token' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->unauthorized('Invalid credentials');
        }

        // Check if user is active
        if ($user->status !== 'active') {
            return $this->forbidden('Your account has been suspended. Please contact support.');
        }

        // Update device token if provided
        if ($request->device_token) {
            $user->update(['device_token' => $request->device_token]);
        }

        // Update last login
        $user->update(['last_login_at' => now()]);

        // Revoke previous tokens (optional - for single device login)
        // $user->tokens()->delete();

        // Create new token
        $token = $user->createToken('mobile-app')->plainTextToken;

        return $this->success([
            'user' => new UserResource($user->load('chefProfile')),
            'token' => $token,
            'token_type' => 'Bearer',
        ], 'Login successful');
    }

    /**
     * Logout user (revoke current token).
     * 
     * POST /api/v1/auth/logout
     */
    public function logout(Request $request)
    {
        // Revoke current token
        $request->user()->currentAccessToken()->delete();

        return $this->success(null, 'Logged out successfully');
    }

    /**
     * Logout from all devices (revoke all tokens).
     * 
     * POST /api/v1/auth/logout-all
     */
    public function logoutAll(Request $request)
    {
        // Revoke all tokens
        $request->user()->tokens()->delete();

        return $this->success(null, 'Logged out from all devices');
    }

    /**
     * Get authenticated user.
     * 
     * GET /api/v1/auth/user
     */
    public function user(Request $request)
    {
        $user = $request->user()->load(['chefProfile', 'addresses', 'activeSubscription']);
        
        return $this->success(new UserResource($user));
    }

    /**
     * Refresh token (issue new token).
     * 
     * POST /api/v1/auth/refresh
     */
    public function refresh(Request $request)
    {
        $user = $request->user();
        
        // Revoke current token
        $request->user()->currentAccessToken()->delete();
        
        // Create new token
        $token = $user->createToken('mobile-app')->plainTextToken;

        return $this->success([
            'token' => $token,
            'token_type' => 'Bearer',
        ], 'Token refreshed');
    }

    /**
     * Send password reset link.
     * 
     * POST /api/v1/auth/forgot-password
     */
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email'],
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return $this->success(null, 'Password reset link sent to your email');
        }

        return $this->error('Unable to send reset link. Please try again.');
    }

    /**
     * Reset password with token.
     * 
     * POST /api/v1/auth/reset-password
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', PasswordRule::defaults()],
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return $this->success(null, 'Password has been reset successfully');
        }

        return $this->error('Unable to reset password. Invalid or expired token.');
    }

    /**
     * Verify email with code (for mobile OTP verification).
     * 
     * POST /api/v1/auth/verify-email
     */
    public function verifyEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => ['required', 'string', 'size:6'],
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $user = $request->user();

        // Check verification code (you may need to implement this model)
        $verification = $user->verificationCodes()
            ->where('code', $request->code)
            ->where('type', 'email')
            ->where('expires_at', '>', now())
            ->first();

        if (!$verification) {
            return $this->error('Invalid or expired verification code', 400);
        }

        // Mark email as verified
        $user->markEmailAsVerified();
        
        // Delete the verification code
        $verification->delete();

        return $this->success(null, 'Email verified successfully');
    }

    /**
     * Resend email verification.
     * 
     * POST /api/v1/auth/resend-verification
     */
    public function resendVerification(Request $request)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return $this->error('Email already verified', 400);
        }

        $user->sendEmailVerificationNotification();

        return $this->success(null, 'Verification email sent');
    }

    /**
     * Update device token for push notifications.
     * 
     * POST /api/v1/auth/device-token
     */
    public function updateDeviceToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_token' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $request->user()->update([
            'device_token' => $request->device_token,
        ]);

        return $this->success(null, 'Device token updated');
    }

    /**
     * Change password for authenticated user.
     * 
     * POST /api/v1/auth/change-password
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => ['required', 'string'],
            'password' => ['required', 'confirmed', PasswordRule::defaults()],
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return $this->error('Current password is incorrect', 400);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return $this->success(null, 'Password changed successfully');
    }

    /**
     * Delete user account.
     * 
     * DELETE /api/v1/auth/account
     */
    public function deleteAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $user = $request->user();

        if (!Hash::check($request->password, $user->password)) {
            return $this->error('Password is incorrect', 400);
        }

        // Revoke all tokens
        $user->tokens()->delete();

        // Soft delete or hard delete based on your preference
        $user->delete();

        return $this->success(null, 'Account deleted successfully');
    }
}
