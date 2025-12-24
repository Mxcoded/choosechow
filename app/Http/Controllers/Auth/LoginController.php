<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Redirect users based on their Role.
     */
    protected function redirectTo()
    {
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            return route('admin.dashboard');
        }

        if ($user->hasRole('chef')) {
            // Check if they need to complete their profile
            if (!$user->chefProfile) {
                return route('chef.profile.edit');
            }
            return route('chef.dashboard');
        }

        // Default for customers
        return route('dashboard');
    }

    /**
     * Check status after the user has been authenticated.
     */
    protected function authenticated(Request $request, $user)
    {
        // Block login if account is not active
        if (in_array($user->status, ['suspended', 'inactive'])) {
            Auth::logout();

            return redirect()->route('login')
                ->withErrors(['email' => 'Your account is currently ' . $user->status . '. Please contact support.']);
        }
    }
}
