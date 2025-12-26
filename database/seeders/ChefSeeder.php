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
                'password' => bcrypt('password'), // Default password
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        // Assign Role
        if (!Role::where('name', 'chef')->exists()) {
            Role::create(['name' => 'chef']);
        }
        $chefUser->assignRole('chef');

        // 2. Create Categories (Ensure they exist)
        $categories = [
            'Main Dish' => Category::firstOrCreate(['name' => 'Main Dish', 'slug' => 'main-dish']),
            'Sides' => Category::firstOrCreate(['name' => 'Sides', 'slug' => 'sides']),
            'Drinks' => Category::firstOrCreate(['name' => 'Drinks', 'slug' => 'drinks']),
        ];

        // 3. Create Cuisines (Ensure they exist)
        $cuisines = [
            'Continental' => Cuisine::firstOrCreate(['name' => 'Continental']),
            'Nigerian' => Cuisine::firstOrCreate(['name' => 'Nigerian']),
            'Spicy' => Cuisine::firstOrCreate(['name' => 'Spicy']),
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
                'is_online' => true,
                'minimum_order' => 2000,
                'delivery_radius_km' => 15,
                'verification_status' => 'verified',
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

        // Attach Cuisines to Profile
        $profile->cuisines()->sync([$cuisines['Continental']->id, $cuisines['Nigerian']->id]);

        // 5. Create Menu Items
        $menuItems = [
            [
                'name' => 'Smokey Jollof Rice & Chicken',
                'description' => 'Classic Nigerian party jollof rice served with grilled chicken thigh and plantains.',
                'price' => 3500,
                'category_id' => $categories['Main Dish']->id,
                'cuisines' => [$cuisines['Nigerian']->id, $cuisines['Spicy']->id],
                'image' => null,
            ],
            [
                'name' => 'Creamy Pasta Alfredo',
                'description' => 'Tagliatelle pasta tossed in a rich parmesan cream sauce with garlic and herbs.',
                'price' => 4500,
                'category_id' => $categories['Main Dish']->id,
                'cuisines' => [$cuisines['Continental']->id],
            ],
            [
                'name' => 'Spicy Asun (Goat Meat)',
                'description' => 'Tender goat meat chunks sautéed in spicy habanero pepper sauce.',
                'price' => 2000,
                'category_id' => $categories['Sides']->id,
                'cuisines' => [$cuisines['Nigerian']->id, $cuisines['Spicy']->id],
            ],
            [
                'name' => 'Chapman Cocktail',
                'description' => 'Refreshing fruity drink with a splash of angostura bitters.',
                'price' => 1500,
                'category_id' => $categories['Drinks']->id,
                'cuisines' => [],
            ]
        ];

        foreach ($menuItems as $item) {
            $menu = Menu::create([
                'chef_id' => $chefUser->id, // FIXED: Was 'user_id'
                'category_id' => $item['category_id'],
                'name' => $item['name'],
                'slug' => Str::slug($item['name']),
                'description' => $item['description'],
                'price' => $item['price'],
                'is_available' => true,
                'is_featured' => rand(0, 1),
                'preparation_time_minutes' => 30,
                'images' => [], // Ensure images array is initialized
            ]);

            if (!empty($item['cuisines'])) {
                $menu->cuisines()->sync($item['cuisines']);
            }
        }

        $this->command->info('Chef "Gourmet Chow" and Menu created successfully!');
    }
}
