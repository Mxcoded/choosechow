<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubscriptionPlan;

class SubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        SubscriptionPlan::updateOrCreate(['slug' => 'basic'], [
            'name' => 'Basic',
            'slug' => 'basic',
            'user_type' => 'customer',
            'description' => '1 free delivery per month, then 10% off delivery fees.',
            'monthly_price' => 1500,
            'yearly_price' => 15000,
            'features' => [
                '1_free_delivery_per_month',
                '10%_delivery_discount_after',
            ],
            'limits' => [
                'free_deliveries' => 1,
                'delivery_discount_percent' => 10,
            ],
            'is_popular' => false,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        SubscriptionPlan::updateOrCreate(['slug' => 'plus'], [
            'name' => 'Plus',
            'slug' => 'plus',
            'user_type' => 'customer',
            'description' => 'Unlimited free delivery on orders ≥ ₦8,000, 15% off partner chef items, priority processing.',
            'monthly_price' => 3500,
            'yearly_price' => 35000,
            'features' => [
                'unlimited_free_delivery',
                '15%_partner_discount',
                'priority_processing',
                'early_access_new_chefs',
                'priority_support',
            ],
            'limits' => [
                'free_delivery_min_subtotal' => 8000,
                'partner_discount_percent' => 15,
            ],
            'is_popular' => true,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        SubscriptionPlan::updateOrCreate(['slug' => 'premium'], [
            'name' => 'Premium',
            'slug' => 'premium',
            'user_type' => 'customer',
            'description' => 'Unlimited free delivery no minimum, 20% off all items, highest priority, dedicated rider, ₦500 monthly wallet credit, VIP support.',
            'monthly_price' => 7500,
            'yearly_price' => 75000,
            'features' => [
                'unlimited_free_delivery',
                '20%_all_discount',
                'highest_order_priority',
                'scheduled_orders',
                'dedicated_rider',
                '500_wallet_credit',
                'vip_support',
            ],
            'limits' => [
                'all_discount_percent' => 20,
                'monthly_wallet_credit' => 500,
                'sla_minutes' => 10,
            ],
            'is_popular' => false,
            'is_active' => true,
            'sort_order' => 3,
        ]);
    }
}
