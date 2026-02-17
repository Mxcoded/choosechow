<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\ChefProfile;
use App\Models\Menu;
use App\Models\Category;
use App\Models\Cuisine;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class ChefSeeder extends Seeder
{
    public function run()
    {
        // 1. Create the Chef User (if not exists)
        $chefUser = User::firstOrCreate(
            ['email' => 'chef@choosechow.com'],
            [
                'first_name' => 'Gordon',
                'last_name' => 'Ramsey',
                'phone' => '08012345678',
                'password' => 'password',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        // Assign Role
        $chefRole = Role::firstOrCreate(['name' => 'chef']);
        if (!$chefUser->hasRole('chef')) {
            $chefUser->assignRole($chefRole);
        }

        // 2. Ensure Categories exist (using slug as unique key)
        $categories = [
            'Main Dish' => Category::firstOrCreate(['slug' => 'main-dish'], ['name' => 'Main Dish']),
            'Sides' => Category::firstOrCreate(['slug' => 'sides'], ['name' => 'Sides']),
            'Drinks' => Category::firstOrCreate(['slug' => 'drinks'], ['name' => 'Drinks']),
        ];

        // 3. Ensure Cuisines exist (using slug as unique key)
        $cuisines = [
            'Continental' => Cuisine::firstOrCreate(['slug' => 'continental'], ['name' => 'Continental']),
            'Nigerian' => Cuisine::firstOrCreate(['slug' => 'nigerian'], ['name' => 'Nigerian']),
            'Spicy' => Cuisine::firstOrCreate(['slug' => 'spicy'], ['name' => 'Spicy']),
        ];

        // 4. Create Chef Profile
        $profile = ChefProfile::updateOrCreate(
            ['user_id' => $chefUser->id],
            [
                'business_name' => 'Gourmet Chow Kitchen',
                'slug' => 'gourmet-chow-kitchen',
                'bio' => 'Experience the finest fusion of local and continental dishes delivered to your doorstep. We cook with passion and serve with love.',
                'years_of_experience' => 10,
                'kitchen_address' => '15 Admiralty Way, Lekki Phase 1, Lagos',
                'city' => 'Lagos',
                'is_online' => true,
                'minimum_order' => 2000,
                'delivery_fee' => 500,
                'delivery_radius_km' => 15,
                'verification_status' => 'verified',
                'is_verified' => true,
                'operating_hours' => [
                    'monday' => ['open' => '09:00', 'close' => '21:00', 'closed' => false],
                    'tuesday' => ['open' => '09:00', 'close' => '21:00', 'closed' => false],
                    'wednesday' => ['open' => '09:00', 'close' => '21:00', 'closed' => false],
                    'thursday' => ['open' => '09:00', 'close' => '21:00', 'closed' => false],
                    'friday' => ['open' => '09:00', 'close' => '22:00', 'closed' => false],
                    'saturday' => ['open' => '10:00', 'close' => '22:00', 'closed' => false],
                    'sunday' => ['open' => '12:00', 'close' => '20:00', 'closed' => false],
                ]
            ]
        );

        // Attach Cuisines to Profile via pivot table
        $profile->cuisines()->sync([
            $cuisines['Continental']->id, 
            $cuisines['Nigerian']->id
        ]);

        // 5. Create Menu Items
        $menuItems = [
            [
                'name' => 'Smokey Jollof Rice & Chicken',
                'description' => 'Classic Nigerian party jollof rice served with grilled chicken thigh and plantains.',
                'price' => 3500,
                'category' => 'Main Dish',
                'cuisines' => [$cuisines['Nigerian']->id, $cuisines['Spicy']->id],
            ],
            [
                'name' => 'Creamy Pasta Alfredo',
                'description' => 'Tagliatelle pasta tossed in a rich parmesan cream sauce with garlic and herbs.',
                'price' => 4500,
                'category' => 'Main Dish',
                'cuisines' => [$cuisines['Continental']->id],
            ],
            [
                'name' => 'Spicy Asun (Goat Meat)',
                'description' => 'Tender goat meat chunks sautéed in spicy habanero pepper sauce.',
                'price' => 2000,
                'category' => 'Sides',
                'cuisines' => [$cuisines['Nigerian']->id, $cuisines['Spicy']->id],
            ],
            [
                'name' => 'Chapman Cocktail',
                'description' => 'Refreshing fruity drink with a splash of angostura bitters.',
                'price' => 1500,
                'category' => 'Drinks',
                'cuisines' => [],
            ]
        ];

        foreach ($menuItems as $item) {
            $menu = Menu::updateOrCreate(
                [
                    'user_id' => $chefUser->id,
                    'slug' => Str::slug($item['name']),
                ],
                [
                    'name' => $item['name'],
                    'description' => $item['description'],
                    'price' => $item['price'],
                    'category' => $item['category'],
                    'is_available' => true,
                    'is_featured' => (bool) rand(0, 1),
                ]
            );

            // Attach cuisines via pivot table
            if (!empty($item['cuisines'])) {
                $menu->cuisines()->sync($item['cuisines']);
            }
        }

        $this->command->info('Chef "Gourmet Chow Kitchen" and Menu created successfully!');
    }
}
