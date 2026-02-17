<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = [
            [
                'first_name' => 'Adaeze',
                'last_name' => 'Okonkwo',
                'email' => 'adaeze@example.com',
                'phone' => '08012345678',
                'gender' => 'female',
                'address' => [
                    'label' => 'Home',
                    'address' => '15 Wuse Zone 5, Abuja',
                    'city' => 'Abuja',
                    'state' => 'FCT',
                    'landmark' => 'Near Wuse Market',
                    'is_default' => true,
                ],
            ],
            [
                'first_name' => 'Chinedu',
                'last_name' => 'Eze',
                'email' => 'chinedu@example.com',
                'phone' => '08023456789',
                'gender' => 'male',
                'address' => [
                    'label' => 'Home',
                    'address' => '23 Victoria Island, Lagos',
                    'city' => 'Lagos',
                    'state' => 'Lagos',
                    'landmark' => 'Opposite Eko Hotel',
                    'is_default' => true,
                ],
            ],
            [
                'first_name' => 'Funke',
                'last_name' => 'Adeleke',
                'email' => 'funke@example.com',
                'phone' => '08034567890',
                'gender' => 'female',
                'address' => [
                    'label' => 'Office',
                    'address' => '10 Lekki Phase 1, Lagos',
                    'city' => 'Lagos',
                    'state' => 'Lagos',
                    'landmark' => 'Inside Lekki Gardens',
                    'is_default' => true,
                ],
            ],
            [
                'first_name' => 'Emeka',
                'last_name' => 'Nwosu',
                'email' => 'emeka@example.com',
                'phone' => '08045678901',
                'gender' => 'male',
                'address' => [
                    'label' => 'Home',
                    'address' => '7 GRA Phase 2, Port Harcourt',
                    'city' => 'Port Harcourt',
                    'state' => 'Rivers',
                    'landmark' => 'Behind Shell Residential',
                    'is_default' => true,
                ],
            ],
            [
                'first_name' => 'Amina',
                'last_name' => 'Bello',
                'email' => 'amina@example.com',
                'phone' => '08056789012',
                'gender' => 'female',
                'address' => [
                    'label' => 'Home',
                    'address' => '45 Maitama District, Abuja',
                    'city' => 'Abuja',
                    'state' => 'FCT',
                    'landmark' => 'Close to Maitama Mosque',
                    'is_default' => true,
                ],
            ],
            [
                'first_name' => 'Tunde',
                'last_name' => 'Bakare',
                'email' => 'tunde@example.com',
                'phone' => '08067890123',
                'gender' => 'male',
                'address' => [
                    'label' => 'Home',
                    'address' => '88 Bodija Estate, Ibadan',
                    'city' => 'Ibadan',
                    'state' => 'Oyo',
                    'landmark' => 'Near UI Second Gate',
                    'is_default' => true,
                ],
            ],
            [
                'first_name' => 'Ngozi',
                'last_name' => 'Ikechukwu',
                'email' => 'ngozi@example.com',
                'phone' => '08078901234',
                'gender' => 'female',
                'address' => [
                    'label' => 'Home',
                    'address' => '12 Independence Layout, Enugu',
                    'city' => 'Enugu',
                    'state' => 'Enugu',
                    'landmark' => 'Behind Polo Park Mall',
                    'is_default' => true,
                ],
            ],
            [
                'first_name' => 'Yusuf',
                'last_name' => 'Mohammed',
                'email' => 'yusuf@example.com',
                'phone' => '08089012345',
                'gender' => 'male',
                'address' => [
                    'label' => 'Home',
                    'address' => '33 Nassarawa GRA, Kano',
                    'city' => 'Kano',
                    'state' => 'Kano',
                    'landmark' => 'Near Kano Polo Club',
                    'is_default' => true,
                ],
            ],
            // Demo customer with easy login
            [
                'first_name' => 'Demo',
                'last_name' => 'Customer',
                'email' => 'customer@chowchow.com',
                'phone' => '08011111111',
                'gender' => 'male',
                'address' => [
                    'label' => 'Home',
                    'address' => '1 Demo Street, Wuse 2',
                    'city' => 'Abuja',
                    'state' => 'FCT',
                    'landmark' => 'Demo Landmark',
                    'is_default' => true,
                ],
            ],
        ];

        foreach ($customers as $customerData) {
            $addressData = $customerData['address'];
            unset($customerData['address']);

            // Check if user already exists by email or phone
            $existingUser = User::where('email', $customerData['email'])
                ->orWhere('phone', $customerData['phone'])
                ->first();

            if ($existingUser) {
                // Update existing user
                $existingUser->update([
                    'first_name' => $customerData['first_name'],
                    'last_name' => $customerData['last_name'],
                    'gender' => $customerData['gender'] ?? null,
                ]);
                $user = $existingUser;
            } else {
                // Create new user
                $user = User::create(array_merge($customerData, [
                    'password' => 'password', // Will be hashed by mutator
                    'email_verified_at' => now(),
                    'status' => 'active',
                    'referral_code' => strtoupper(Str::random(8)),
                ]));
            }

            // Assign customer role
            if (!$user->hasRole('customer')) {
                $user->assignRole('customer');
            }

            // Create default address
            UserAddress::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'label' => $addressData['label'],
                ],
                [
                    'street_address' => $addressData['address'],
                    'city' => $addressData['city'],
                    'state' => $addressData['state'],
                    'delivery_instructions' => $addressData['landmark'] ?? null,
                    'is_default' => $addressData['is_default'] ?? false,
                    'type' => 'delivery',
                    'country' => 'Nigeria',
                ]
            );

            $this->command->info("✓ Created customer: {$user->full_name} ({$user->email})");
        }

        $this->command->newLine();
        $this->command->info("🎉 Created " . count($customers) . " customers!");
        $this->command->info("📧 Demo login: customer@chowchow.com / password");
    }
}
