<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        $user = User::firstOrCreate(
            ['email' => 'admin@choosechow.com'],
            [
                'first_name' => 'Mezabu',
                'last_name' => 'Xcoded',
                'password' => '#Pwd123#',
                'phone' => '1234567890',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        if (!$user->hasRole('admin')) {
            $user->assignRole('admin');
        }
    }
}
