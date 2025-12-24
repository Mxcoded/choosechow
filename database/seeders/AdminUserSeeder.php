<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        // 1. Create the User
        $user = User::create([
            'first_name' => 'Mezabu',
            'last_name' => 'Xcoded',
            'email' => 'admin@choosechow.com',
            'password' => '#Pwd123#', // Model mutator will hash this automatically
            'phone' => '1234567890',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // 2. Assign the Spatie Role
        $user->assignRole('admin');
    }
}
