<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Cuisine;
use App\Models\DietaryPreference;
use Illuminate\Support\Str;

class TaxonomySeeder extends Seeder
{
    public function run(): void
    {
        // 1. Categories
        $categories = [
            'Main Course',
            'Appetizer',
            'Dessert',
            'Beverage',
            'Soup',
            'Swallow',
            'Breakfast',
            'Grills',
            'Sides',
            'Snacks',
            'Pastries'
        ];

        foreach ($categories as $name) {
            Category::firstOrCreate(['slug' => Str::slug($name)], ['name' => $name]);
        }

        // 2. Cuisines (Nigerian & International)
        $cuisines = [
            'Nigerian (General)',
            'Igbo',
            'Yoruba',
            'Hausa',
            'Edo',
            'Calabar',
            'Continental',
            'Chinese',
            'Italian',
            'Indian',
            'American',
            'Mediterranean',
            'Asian Fusion',
            'Street Food'
        ];

        foreach ($cuisines as $name) {
            Cuisine::firstOrCreate(['slug' => Str::slug($name)], ['name' => $name]);
        }

        // 3. Dietary Preferences
        $dietaries = [
            'Vegetarian',
            'Vegan',
            'Gluten-Free',
            'Halal',
            'Kosher',
            'Nut-Free',
            'Dairy-Free',
            'Spicy',
            'Extra Spicy',
            'Keto',
            'Sugar-Free',
            'Low Carb'
        ];

        foreach ($dietaries as $name) {
            DietaryPreference::firstOrCreate(['slug' => Str::slug($name)], ['name' => $name]);
        }
    }
}
