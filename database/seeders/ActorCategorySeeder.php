<?php

namespace Database\Seeders;

use App\Models\ActorCategory;
use Illuminate\Database\Seeder;

class ActorCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Home Chef',
                'slug' => 'home-chef',
                'description' => 'Cook and sell homemade meals from your kitchen',
                'icon' => 'fa-utensils',
                'sort_order' => 1,
            ],
            [
                'name' => 'Food Truck',
                'slug' => 'food-truck',
                'description' => 'Mobile food vendor with a food truck or cart',
                'icon' => 'fa-truck',
                'sort_order' => 2,
            ],
            [
                'name' => 'Local Market Vendor',
                'slug' => 'market-vendor',
                'description' => 'Sell food at local markets or stalls',
                'icon' => 'fa-store',
                'sort_order' => 3,
            ],
            [
                'name' => 'Caterer',
                'slug' => 'caterer',
                'description' => 'Professional catering services for events',
                'icon' => 'fa-concierge-bell',
                'sort_order' => 4,
            ],
            [
                'name' => 'Baker',
                'slug' => 'baker',
                'description' => 'Specialize in baked goods, pastries, and desserts',
                'icon' => 'fa-bread-slice',
                'sort_order' => 5,
            ],
            [
                'name' => 'Small Chops Vendor',
                'slug' => 'small-chops',
                'description' => 'Specialize in small chops and party snacks',
                'icon' => 'fa-cookie-bite',
                'sort_order' => 6,
            ],
            [
                'name' => 'Drinks & Smoothies',
                'slug' => 'drinks-smoothies',
                'description' => 'Fresh juices, smoothies, and beverages',
                'icon' => 'fa-blender',
                'sort_order' => 7,
            ],
            [
                'name' => 'Other',
                'slug' => 'other',
                'description' => 'Other food-related business',
                'icon' => 'fa-question-circle',
                'sort_order' => 99,
            ],
        ];

        foreach ($categories as $category) {
            ActorCategory::updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}
