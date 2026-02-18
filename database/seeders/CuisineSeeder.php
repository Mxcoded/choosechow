<?php

namespace Database\Seeders;

use App\Models\Cuisine;
use Illuminate\Database\Seeder;

class CuisineSeeder extends Seeder
{
    public function run(): void
    {
        $cuisines = [
            'Rice Dishes',
            'Swallow & Soups',
            'Pasta',
            'Grills & BBQ',
            'Pastries',
            'Breakfast',
            'Continental',
            'Vegetarian',
            'Drinks & Smoothies',
            'Fast Food',
            'Local Delicacies',
            'Seafood',
        ];

        foreach ($cuisines as $cuisine) {
            Cuisine::firstOrCreate(['name' => $cuisine]);
        }
    }
}
