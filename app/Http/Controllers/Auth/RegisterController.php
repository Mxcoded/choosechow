<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     */
    protected function redirectTo()
    {
        if (auth()->user()->hasRole('chef')) {
            return route('chef.profile.edit'); // Redirect new chefs to complete profile
        }
        return route('dashboard');
    }

    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'max:20', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'user_type' => ['required', 'string', 'in:customer,chef'], // Matches your blade form
            'terms' => ['required', 'accepted'],
            'referred_by' => ['nullable', 'string', 'exists:users,referral_code'], // Validates referral code
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     */
    protected function create(array $data)
    {
        // 1. Create User (Pass Plain Password -> Model Mutator handles hashing)
        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => $data['password'], // Plain text here!
            'phone' => $data['phone'] ?? null,
            'referred_by' => $data['referred_by'] ?? null,
            'status' => 'active', // Ensure status is active so they can login
        ]);

        // 2. Assign Spatie Role (CRITICAL: DB column 'user_type' is gone, so we must use Roles)
        $role = $data['user_type'] ?? 'customer';
        $user->assignRole($role);

        return $user;
    }
}
