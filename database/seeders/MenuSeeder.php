<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Menu;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

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
            $chef = User::create([
                'name' => 'Chef Demo',
                'email' => 'chef@demo.com',
                'password' => bcrypt('password'),
                'role' => 'chef',
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
                    'calories' => 650,
                    'protein' => '35g',
                    'carbs' => '75g',
                    'fat' => '18g',
                    'fiber' => '4g'
                ],
                'cooking_instructions' => 'Marinate chicken with spices for 30 minutes. Grill chicken until golden. Prepare jollof rice base with blended tomatoes and peppers. Add rice and stock, simmer until tender.',
                'storage_instructions' => 'Store in refrigerator for up to 3 days. Reheat thoroughly before serving.',
                'availability_schedule' => [
                    'Monday' => '11:00 AM - 9:00 PM',
                    'Tuesday' => '11:00 AM - 9:00 PM',
                    'Wednesday' => '11:00 AM - 9:00 PM',
                    'Thursday' => '11:00 AM - 9:00 PM',
                    'Friday' => '11:00 AM - 10:00 PM',
                    'Saturday' => '12:00 PM - 10:00 PM',
                    'Sunday' => '12:00 PM - 8:00 PM'
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
                    'calories' => 780,
                    'protein' => '42g',
                    'carbs' => '85g',
                    'fat' => '28g'
                ],
                'cooking_instructions' => 'Boil yam until soft, pound until smooth. Prepare egusi soup with ground seeds, palm oil, and vegetables. Cook meat and fish separately before adding to soup.',
                'storage_instructions' => 'Best consumed fresh. Soup can be stored for 2 days in refrigerator.',
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
                    'calories' => 420,
                    'protein' => '38g',
                    'carbs' => '8g',
                    'fat' => '25g'
                ],
                'cooking_instructions' => 'Clean catfish thoroughly. Boil with spices and aromatics until tender. Add scent leaves at the end for fresh flavor.',
                'storage_instructions' => 'Consume within 24 hours for best taste. Store in refrigerator.',
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
                    'calories' => 380,
                    'protein' => '28g',
                    'carbs' => '6g',
                    'fat' => '26g'
                ],
                'cooking_instructions' => 'Marinate beef in suya spice for 2 hours. Thread onto skewers and grill over medium heat, turning frequently.',
                'storage_instructions' => 'Best consumed immediately. Can be stored for 1 day in refrigerator.',
                'stock_quantity' => 30,
                'is_available' => true,
                'is_featured' => true,
                'featured_until' => now()->addDays(15),
            ],
            [
                'name' => 'Puff Puff',
                'description' => 'Golden, fluffy Nigerian doughnuts that are crispy on the outside and soft on the inside. Perfect with a cup of tea or as a sweet snack.',
                'price' => 1500.00,
                'category' => 'appetizer',
                'cuisine_types' => ['Nigerian', 'Snack'],
                'dietary_info' => ['Vegetarian'],
                'spice_level' => 0,
                'preparation_time_minutes' => 30,
                'serves_count' => 4,
                'ingredients' => ['Flour', 'Sugar', 'Yeast', 'Salt', 'Nutmeg', 'Vegetable Oil'],
                'allergens' => ['Gluten'],
                'nutritional_info' => [
                    'calories' => 220,
                    'protein' => '4g',
                    'carbs' => '35g',
                    'fat' => '8g'
                ],
                'cooking_instructions' => 'Mix ingredients to form smooth batter. Allow to rise for 1 hour. Deep fry spoonfuls until golden brown.',
                'storage_instructions' => 'Best consumed fresh. Can be stored for 2 days in airtight container.',
                'stock_quantity' => 50,
                'is_available' => true,
            ],

            // Beverages
            [
                'name' => 'Zobo Drink',
                'description' => 'Refreshing Nigerian herbal drink made with hibiscus leaves and natural fruits. Packed with antioxidants and bursting with flavor.',
                'price' => 1200.00,
                'category' => 'beverage',
                'cuisine_types' => ['Nigerian', 'Herbal'],
                'dietary_info' => ['Vegan', 'Gluten-Free', 'Dairy-Free', 'Sugar-Free'],
                'spice_level' => 0,
                'preparation_time_minutes' => 15,
                'serves_count' => 1,
                'ingredients' => ['Hibiscus Leaves', 'Ginger', 'Pineapple', 'Cucumber', 'Orange', 'Lemon', 'Mint'],
                'allergens' => [],
                'nutritional_info' => [
                    'calories' => 45,
                    'protein' => '1g',
                    'carbs' => '11g',
                    'fat' => '0g',
                    'vitamin_c' => '25mg'
                ],
                'cooking_instructions' => 'Boil hibiscus leaves with ginger. Strain and add fresh fruit juices. Chill before serving.',
                'storage_instructions' => 'Store in refrigerator for up to 3 days. Serve chilled.',
                'stock_quantity' => 40,
                'is_available' => true,
            ],
            [
                'name' => 'Palm Wine',
                'description' => 'Traditional Nigerian alcoholic beverage tapped fresh from palm trees. Sweet, refreshing, and naturally fermented.',
                'price' => 2000.00,
                'category' => 'beverage',
                'cuisine_types' => ['Nigerian', 'Traditional'],
                'dietary_info' => ['Gluten-Free', 'Dairy-Free'],
                'spice_level' => 0,
                'preparation_time_minutes' => 5,
                'serves_count' => 1,
                'ingredients' => ['Fresh Palm Wine'],
                'allergens' => [],
                'nutritional_info' => [
                    'calories' => 120,
                    'protein' => '1g',
                    'carbs' => '15g',
                    'alcohol' => '4%'
                ],
                'cooking_instructions' => 'Serve fresh and chilled. No cooking required.',
                'storage_instructions' => 'Best consumed within 24 hours of tapping. Keep refrigerated.',
                'stock_quantity' => 20,
                'is_available' => true,
            ],

            // Desserts
            [
                'name' => 'Chin Chin',
                'description' => 'Crunchy Nigerian snack made from fried dough, lightly sweetened and perfect for any time of day. A beloved treat across West Africa.',
                'price' => 1800.00,
                'category' => 'dessert',
                'cuisine_types' => ['Nigerian', 'West African'],
                'dietary_info' => ['Vegetarian'],
                'spice_level' => 0,
                'preparation_time_minutes' => 40,
                'serves_count' => 3,
                'ingredients' => ['Flour', 'Sugar', 'Butter', 'Eggs', 'Milk', 'Baking Powder', 'Nutmeg', 'Vegetable Oil'],
                'allergens' => ['Gluten', 'Eggs', 'Dairy'],
                'nutritional_info' => [
                    'calories' => 180,
                    'protein' => '3g',
                    'carbs' => '25g',
                    'fat' => '8g'
                ],
                'cooking_instructions' => 'Mix ingredients to form dough. Roll and cut into small cubes. Deep fry until golden and crispy.',
                'storage_instructions' => 'Store in airtight container for up to 1 week.',
                'stock_quantity' => 35,
                'is_available' => true,
            ],

            // Snacks
            [
                'name' => 'Meat Pie',
                'description' => 'Flaky pastry filled with seasoned minced meat, potatoes, and carrots. A popular Nigerian snack perfect for any occasion.',
                'price' => 2200.00,
                'category' => 'snack',
                'cuisine_types' => ['Nigerian', 'British-Nigerian'],
                'dietary_info' => [],
                'spice_level' => 1,
                'preparation_time_minutes' => 50,
                'serves_count' => 1,
                'ingredients' => ['Flour', 'Butter', 'Minced Beef', 'Potatoes', 'Carrots', 'Onions', 'Seasoning', 'Eggs'],
                'allergens' => ['Gluten', 'Eggs', 'Dairy'],
                'nutritional_info' => [
                    'calories' => 420,
                    'protein' => '18g',
                    'carbs' => '35g',
                    'fat' => '24g'
                ],
                'cooking_instructions' => 'Prepare pastry and filling separately. Assemble pies and bake until golden brown.',
                'storage_instructions' => 'Store in refrigerator for up to 3 days. Reheat before serving.',
                'stock_quantity' => 25,
                'is_available' => true,
            ],
            [
                'name' => 'Akara (Bean Cakes)',
                'description' => 'Deep-fried bean cakes made from black-eyed peas, onions, and peppers. A protein-rich Nigerian breakfast favorite.',
                'price' => 1600.00,
                'category' => 'snack',
                'cuisine_types' => ['Nigerian', 'Yoruba'],
                'dietary_info' => ['Vegan', 'Gluten-Free', 'High-Protein'],
                'spice_level' => 2,
                'preparation_time_minutes' => 35,
                'serves_count' => 2,
                'ingredients' => ['Black-eyed Peas', 'Onions', 'Peppers', 'Salt', 'Vegetable Oil'],
                'allergens' => [],
                'nutritional_info' => [
                    'calories' => 280,
                    'protein' => '12g',
                    'carbs' => '20g',
                    'fat' => '16g',
                    'fiber' => '8g'
                ],
                'cooking_instructions' => 'Soak and peel beans. Blend with peppers and onions. Deep fry spoonfuls until golden.',
                'storage_instructions' => 'Best consumed fresh. Can be stored for 1 day in refrigerator.',
                'stock_quantity' => 30,
                'is_available' => true,
                'is_featured' => true,
                'featured_until' => now()->addDays(20),
            ],
        ];

        foreach ($menuItems as $item) {
            // Randomly assign to a chef
            $chef = $chefs->random();
            
            Menu::create(array_merge($item, [
                'chef_id' => $chef->id,
                'images' => [], // You can add sample image paths here if needed
                'created_at' => now()->subDays(rand(1, 30)),
                'updated_at' => now()->subDays(rand(0, 5)),
            ]));
        }

        $this->command->info('Menu items seeded successfully!');
        $this->command->info('Created ' . count($menuItems) . ' menu items for ' . $chefs->count() . ' chef(s)');
    }
}