<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Menu;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str; // Added Str import for slug generation

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all chef users (user_type 'chef')
        $chefs = User::where('user_type', 'chef')->get();

        // If no chefs exist, create a default one
        if ($chefs->isEmpty()) {
            // NOTE: Assuming your User model has 'role' or similar for user_type
            $chef = User::create([
                'name' => 'Chef Demo',
                'email' => 'chef@demo.com',
                'password' => bcrypt('password'),
                'user_type' => 'chef', // Assuming the user type is stored here
                'email_verified_at' => now(),
            ]);
            $chefs = collect([$chef]);
        }

        $menuItems = [
            // Main Courses
            [
                'name' => 'Jollof Rice with Grilled Chicken',
                'description' => 'Aromatic Nigerian jollof rice cooked in rich tomato sauce, served with perfectly grilled chicken seasoned with local spices. A true taste of Nigeria!',
                'price' => 3500.00,
                'discounted_price' => 3000.00,
                'category' => 'main',
                'cuisine_types' => ['Nigerian', 'West African'],
                'dietary_info' => ['Gluten-Free', 'Dairy-Free'],
                'spice_level' => 2,
                'preparation_time_minutes' => 45,
                'serves_count' => 1,
                'ingredients' => ['Rice', 'Chicken', 'Tomatoes', 'Onions', 'Bell Peppers', 'Scotch Bonnet', 'Ginger', 'Garlic', 'Bay Leaves', 'Thyme'],
                'allergens' => [],
                'nutritional_info' => [
                    'Calories' => '650',
                    'Protein' => '35g',
                    'Carbs' => '75g',
                    'Fat' => '18g',
                    'Fiber' => '4g'
                ],
                'cooking_instructions' => 'Marinate chicken with spices for 30 minutes. Grill chicken until golden. Prepare jollof rice base with blended tomatoes and peppers. Add rice and stock, simmer until tender.',
                'storage_instructions' => 'Store in refrigerator for up to 3 days. Reheat thoroughly before serving.',
                'raw_schedule' => [ // Use a temporary key for the schedule
                    'mon' => '09:00-21:00',
                    'tue' => '09:00-21:00',
                    'wed' => '09:00-21:00',
                    'thu' => '09:00-21:00',
                    'fri' => '09:00-22:00',
                    'sat' => '10:00-22:00',
                    'sun' => '10:00-20:00'
                ],
                'stock_quantity' => 25,
                'is_available' => true,
                'is_featured' => true,
                'featured_until' => now()->addDays(30),
            ],
            [
                'name' => 'Pounded Yam with Egusi Soup',
                'description' => 'Traditional pounded yam served with rich egusi soup made with ground melon seeds, spinach, and assorted meat. A Nigerian delicacy that satisfies the soul.',
                'price' => 4200.00,
                'category' => 'main',
                'cuisine_types' => ['Nigerian', 'Traditional'],
                'dietary_info' => ['Gluten-Free'],
                'spice_level' => 3,
                'preparation_time_minutes' => 60,
                'serves_count' => 1,
                'ingredients' => ['Yam', 'Egusi Seeds', 'Spinach', 'Beef', 'Fish', 'Palm Oil', 'Onions', 'Pepper', 'Seasoning'],
                'allergens' => ['Fish'],
                'nutritional_info' => [
                    'Calories' => '780',
                    'Protein' => '42g',
                    'Carbs' => '85g',
                    'Fat' => '28g'
                ],
                'cooking_instructions' => 'Boil yam until soft, pound until smooth. Prepare egusi soup with ground seeds, palm oil, and vegetables. Cook meat and fish separately before adding to soup.',
                'storage_instructions' => 'Best consumed fresh. Soup can be stored for 2 days in refrigerator.',
                'raw_schedule' => [
                    'mon' => '10:00-20:00',
                    'tue' => '10:00-20:00',
                    'wed' => '10:00-20:00',
                    'thu' => '10:00-20:00',
                    'fri' => '10:00-22:00',
                    'sat' => '12:00-22:00',
                    'sun' => null // Unavailable on Sunday
                ],
                'stock_quantity' => 15,
                'is_available' => true,
                'is_featured' => false,
            ],
            [
                'name' => 'Pepper Soup with Catfish',
                'description' => 'Spicy and aromatic Nigerian pepper soup with fresh catfish, infused with traditional herbs and spices. Perfect for cold days or when you need comfort food.',
                'price' => 3800.00,
                'category' => 'main',
                'cuisine_types' => ['Nigerian'],
                'dietary_info' => ['Gluten-Free', 'Dairy-Free', 'Low-Carb'],
                'spice_level' => 4,
                'preparation_time_minutes' => 35,
                'serves_count' => 1,
                'ingredients' => ['Catfish', 'Pepper Soup Spice', 'Ginger', 'Garlic', 'Onions', 'Scent Leaves', 'Uziza Seeds'],
                'allergens' => ['Fish'],
                'nutritional_info' => [
                    'Calories' => '420',
                    'Protein' => '38g',
                    'Carbs' => '8g',
                    'Fat' => '25g'
                ],
                'cooking_instructions' => 'Clean catfish thoroughly. Boil with spices and aromatics until tender. Add scent leaves at the end for fresh flavor.',
                'storage_instructions' => 'Consume within 24 hours for best taste. Store in refrigerator.',
                'raw_schedule' => [
                    'mon' => '12:00-21:00',
                    'tue' => '12:00-21:00',
                    'wed' => '12:00-21:00',
                    'thu' => '12:00-21:00',
                    'fri' => '12:00-22:00',
                    'sat' => '12:00-22:00',
                    'sun' => '12:00-20:00'
                ],
                'stock_quantity' => 12,
                'is_available' => true,
            ],

            // Appetizers
            [
                'name' => 'Suya Skewers',
                'description' => 'Grilled beef skewers marinated in traditional suya spice blend. A popular Nigerian street food that\'s perfect as an appetizer or snack.',
                'price' => 2500.00,
                'discounted_price' => 2200.00,
                'category' => 'appetizer',
                'cuisine_types' => ['Nigerian', 'Street Food'],
                'dietary_info' => ['Gluten-Free', 'Dairy-Free'],
                'spice_level' => 3,
                'preparation_time_minutes' => 25,
                'serves_count' => 2,
                'ingredients' => ['Beef', 'Suya Spice', 'Groundnuts', 'Ginger', 'Garlic', 'Onions', 'Vegetable Oil'],
                'allergens' => ['Nuts'],
                'nutritional_info' => [
                    'Calories' => '380',
                    'Protein' => '28g',
                    'Carbs' => '6g',
                    'Fat' => '26g'
                ],
                'cooking_instructions' => 'Marinate beef in suya spice for 2 hours. Thread onto skewers and grill over medium heat, turning frequently.',
                'storage_instructions' => 'Best consumed immediately. Can be stored for 1 day in refrigerator.',
                'raw_schedule' => [
                    'mon' => '15:00-22:00',
                    'tue' => '15:00-22:00',
                    'wed' => '15:00-22:00',
                    'thu' => '15:00-22:00',
                    'fri' => '15:00-23:00',
                    'sat' => '15:00-23:00',
                    'sun' => '15:00-21:00'
                ],
                'stock_quantity' => 30,
                'is_available' => true,
                'is_featured' => true,
                'featured_until' => now()->addDays(15),
            ],
            // ... (omitted Puff Puff, Zobo, Palm Wine, Chin Chin, Meat Pie, Akara for brevity, but they would follow the same raw_schedule structure) ...
        ];

        foreach ($menuItems as $item) {
            // Randomly assign to a chef
            $chef = $chefs->random();

            // 1. Process raw_schedule into the structured JSON array format
            $processedSchedule = [];
            foreach ($item['raw_schedule'] as $day => $timeRange) {
                if ($timeRange) {
                    [$start, $end] = explode('-', $timeRange);
                    $processedSchedule[$day] = [
                        'available' => true,
                        'start_time' => trim($start),
                        'end_time' => trim($end),
                    ];
                } else {
                    $processedSchedule[$day] = ['available' => false];
                }
            }

            // 2. Prepare data for creation
            $data = array_merge($item, [
                'chef_id' => $chef->id,
                'images' => [],
                'availability_schedule' => $processedSchedule, // Use the structured array
                'slug' => Str::slug($item['name'] . '-' . Str::random(6)), // Ensure slug is generated
                'created_at' => now()->subDays(rand(1, 30)),
                'updated_at' => now()->subDays(rand(0, 5)),
            ]);

            // 3. Remove temporary key before creation
            unset($data['raw_schedule']);

            Menu::create($data);
        }

        $this->command->info('Menu items seeded successfully!');
        $this->command->info('Created ' . count($menuItems) . ' menu items for ' . $chefs->count() . ' chef(s)');
    }

    // Helper method to convert the schedule string if needed, or simply embed the logic as above
}
