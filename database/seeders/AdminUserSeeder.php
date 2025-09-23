<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'first_name' => 'Mezabu',
            'last_name' => 'Xcoded',
            'email' => 'demo@choosechow.com',
            'password' => '#Pwd123#', // Will be hashed by the model
            'phone' => '1234567890',
            'user_type' => 'admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
    }
}
