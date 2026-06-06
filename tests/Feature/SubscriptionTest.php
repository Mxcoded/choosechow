<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\Models\ChefProfile;
use App\Models\Wallet;
use App\Models\WalletTransactionLog;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use App\Services\DiscountService;
use App\Services\SubscriptionService;
use App\Services\BillingService;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use PHPUnit\Framework\Attributes\Test;

class SubscriptionTest extends TestCase
{
    use RefreshDatabase;

    protected User $customer;
    protected DiscountService $discountService;
    protected SubscriptionService $subscriptionService;
    protected BillingService $billingService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([ValidateCsrfToken::class]);

        Role::firstOrCreate(['name' => 'customer']);
        Role::firstOrCreate(['name' => 'chef']);
        Role::firstOrCreate(['name' => 'rider']);

        $this->customer = User::factory()->create(['status' => 'active']);
        $this->customer->assignRole('customer');

        $this->seedPlans();

        $this->discountService = $this->app->make(DiscountService::class);
        $this->subscriptionService = $this->app->make(SubscriptionService::class);
        $this->billingService = $this->app->make(BillingService::class);
    }

    protected function seedPlans(): void
    {
        SubscriptionPlan::create([
            'name' => 'Basic',
            'slug' => 'basic',
            'user_type' => 'customer',
            'description' => '1 free delivery per month',
            'monthly_price' => 1500,
            'yearly_price' => 15000,
            'features' => ['1_free_delivery_per_month', '10%_delivery_discount_after'],
            'limits' => ['free_deliveries' => 1, 'delivery_discount_percent' => 10],
            'is_active' => true,
            'sort_order' => 1,
        ]);

        SubscriptionPlan::create([
            'name' => 'Plus',
            'slug' => 'plus',
            'user_type' => 'customer',
            'description' => 'Unlimited free delivery on orders ≥ ₦8,000, 15% off partner chef items',
            'monthly_price' => 3500,
            'yearly_price' => 35000,
            'features' => ['unlimited_free_delivery', '15%_partner_discount', 'priority_processing'],
            'limits' => ['free_delivery_min_subtotal' => 8000, 'partner_discount_percent' => 15],
            'is_active' => true,
            'is_popular' => true,
            'sort_order' => 2,
        ]);

        SubscriptionPlan::create([
            'name' => 'Premium',
            'slug' => 'premium',
            'user_type' => 'customer',
            'description' => 'Unlimited free delivery no minimum, 20% off all items',
            'monthly_price' => 7500,
            'yearly_price' => 75000,
            'features' => ['unlimited_free_delivery', '20%_all_discount', 'highest_order_priority', 'dedicated_rider'],
            'limits' => ['all_discount_percent' => 20, 'monthly_wallet_credit' => 500],
            'is_active' => true,
            'sort_order' => 3,
        ]);
    }

    // ========== DELIVERY FEE TESTS ==========

    #[Test]
    public function non_subscriber_pays_standard_delivery_fee(): void
    {
        $fee = $this->discountService->calculateDeliveryFee(1000, $this->customer);
        $this->assertEquals(1000, $fee);
    }

    #[Test]
    public function premium_user_gets_free_delivery(): void
    {
        $this->setSubscription('premium');
        $fee = $this->discountService->calculateDeliveryFee(1000, $this->customer);
        $this->assertEquals(0, $fee);
    }

    #[Test]
    public function basic_user_gets_first_delivery_free(): void
    {
        $this->setSubscription('basic');
        $fee = $this->discountService->calculateDeliveryFee(1000, $this->customer);
        $this->assertEquals(0, $fee);
    }

    #[Test]
    public function basic_user_gets_ten_percent_off_second_delivery(): void
    {
        $this->setSubscription('basic');
        $this->customer->increment('free_delivery_used');
        $fee = $this->discountService->calculateDeliveryFee(1000, $this->customer);
        $this->assertEquals(900, $fee);
    }

    #[Test]
    public function plus_user_pays_delivery_below_minimum(): void
    {
        $this->setSubscription('plus');
        $free = $this->discountService->isPlusDeliveryFree(7000);
        $this->assertFalse($free);

        $fee = $this->discountService->calculateDeliveryFee(1000, $this->customer);
        $this->assertEquals(1000, $fee);
    }

    #[Test]
    public function plus_user_gets_free_delivery_at_minimum(): void
    {
        $this->setSubscription('plus');
        $free = $this->discountService->isPlusDeliveryFree(8000);
        $this->assertTrue($free);
    }

    // ========== ITEM DISCOUNT TESTS ==========

    #[Test]
    public function non_subscriber_gets_no_item_discount(): void
    {
        $discount = $this->discountService->calculateItemDiscount(2000, $this->customer);
        $this->assertEquals(0, $discount);
    }

    #[Test]
    public function premium_gets_twenty_percent_off_all_items(): void
    {
        $this->setSubscription('premium');
        $discount = $this->discountService->calculateItemDiscount(2000, $this->customer);
        $this->assertEquals(400, $discount);
    }

    #[Test]
    public function plus_gets_fifteen_percent_off_partner_items(): void
    {
        $this->setSubscription('plus');
        $discount = $this->discountService->calculateItemDiscount(2000, $this->customer, true);
        $this->assertEquals(300, $discount);
    }

    #[Test]
    public function plus_gets_no_discount_on_non_partner_items(): void
    {
        $this->setSubscription('plus');
        $discount = $this->discountService->calculateItemDiscount(2000, $this->customer, false);
        $this->assertEquals(0, $discount);
    }

    // ========== ORDER PRIORITY TESTS ==========

    #[Test]
    public function non_subscriber_has_lowest_priority(): void
    {
        $this->assertEquals(1, $this->discountService->getOrderPriority($this->customer));
    }

    #[Test]
    public function premium_has_highest_priority(): void
    {
        $this->setSubscription('premium');
        $this->assertEquals(3, $this->discountService->getOrderPriority($this->customer));
    }

    #[Test]
    public function plus_has_medium_priority(): void
    {
        $this->setSubscription('plus');
        $this->assertEquals(2, $this->discountService->getOrderPriority($this->customer));
    }

    // ========== SLA TESTS ==========

    #[Test]
    public function non_subscriber_gets_standard_sla(): void
    {
        $this->assertEquals('standard', $this->discountService->getSlaLevel($this->customer));
        $this->assertEquals(1440, $this->discountService->getSlaMinutes($this->customer));
    }

    #[Test]
    public function premium_gets_vip_sla(): void
    {
        $this->setSubscription('premium');
        $this->assertEquals('vip', $this->discountService->getSlaLevel($this->customer));
        $this->assertEquals(10, $this->discountService->getSlaMinutes($this->customer));
    }

    #[Test]
    public function plus_gets_priority_sla(): void
    {
        $this->setSubscription('plus');
        $this->assertEquals('priority', $this->discountService->getSlaLevel($this->customer));
        $this->assertEquals(60, $this->discountService->getSlaMinutes($this->customer));
    }

    // ========== SUBSCRIPTION LIFECYCLE TESTS ==========

    #[Test]
    public function user_can_subscribe_to_basic(): void
    {
        $result = $this->subscriptionService->subscribe($this->customer, 'basic');
        $this->assertTrue($result['success']);

        $this->customer->refresh();
        $this->assertEquals('basic', $this->customer->subscription_tier);
        $this->assertEquals('active', $this->customer->subscription_status);
    }

    #[Test]
    public function user_can_upgrade_from_basic_to_plus(): void
    {
        $this->subscriptionService->subscribe($this->customer, 'basic');
        $result = $this->subscriptionService->upgrade($this->customer, 'plus');
        $this->assertTrue($result['success']);

        $this->customer->refresh();
        $this->assertEquals('plus', $this->customer->subscription_tier);
    }

    #[Test]
    public function user_can_upgrade_from_plus_to_premium(): void
    {
        $this->subscriptionService->subscribe($this->customer, 'plus');
        $result = $this->subscriptionService->upgrade($this->customer, 'premium');
        $this->assertTrue($result['success']);

        $this->customer->refresh();
        $this->assertEquals('premium', $this->customer->subscription_tier);
    }

    #[Test]
    public function user_can_downgrade_from_premium_to_plus(): void
    {
        $this->subscriptionService->subscribe($this->customer, 'premium');
        $result = $this->subscriptionService->downgrade($this->customer, 'plus');
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('effective_at', $result);
    }

    #[Test]
    public function user_can_downgrade_from_plus_to_basic(): void
    {
        $this->subscriptionService->subscribe($this->customer, 'plus');
        $result = $this->subscriptionService->downgrade($this->customer, 'basic');
        $this->assertTrue($result['success']);
    }

    #[Test]
    public function user_can_cancel_subscription(): void
    {
        $this->subscriptionService->subscribe($this->customer, 'premium');
        $result = $this->subscriptionService->cancel($this->customer);
        $this->assertTrue($result['success']);

        $this->customer->refresh();
        $this->assertEquals('cancelled', $this->customer->subscription_status);
    }

    #[Test]
    public function user_can_cancel_subscription_immediately(): void
    {
        $this->subscriptionService->subscribe($this->customer, 'premium');
        $result = $this->subscriptionService->cancel($this->customer, true);
        $this->assertTrue($result['success']);

        $this->customer->refresh();
        $this->assertEquals('none', $this->customer->subscription_tier);
        $this->assertEquals('cancelled', $this->customer->subscription_status);
    }

    // ========== EDGE CASE TESTS ==========

    #[Test]
    public function plus_user_at_7999_pays_delivery(): void
    {
        $this->setSubscription('plus');
        $free = $this->discountService->isPlusDeliveryFree(7999);
        $this->assertFalse($free);
    }

    #[Test]
    public function plus_user_at_8000_gets_free_delivery(): void
    {
        $this->setSubscription('plus');
        $free = $this->discountService->isPlusDeliveryFree(8000);
        $this->assertTrue($free);
    }

    #[Test]
    public function basic_user_second_order_gets_discount_not_free(): void
    {
        $this->setSubscription('basic');
        $this->customer->free_delivery_used = 1;
        $this->customer->save();

        $fee = $this->discountService->calculateDeliveryFee(1000, $this->customer);
        $this->assertGreaterThan(0, $fee);
        $this->assertEquals(900, $fee);
    }

    #[Test]
    public function non_subscriber_cannot_schedule_orders(): void
    {
        $this->assertFalse($this->discountService->canScheduleOrders($this->customer));
    }

    #[Test]
    public function premium_user_can_schedule_orders(): void
    {
        $this->setSubscription('premium');
        $this->assertTrue($this->discountService->canScheduleOrders($this->customer));
    }

    #[Test]
    public function upgrade_fails_if_already_on_tier(): void
    {
        $this->setSubscription('premium');
        $result = $this->subscriptionService->upgrade($this->customer, 'premium');
        $this->assertFalse($result['success']);
    }

    #[Test]
    public function downgrade_fails_if_already_on_tier(): void
    {
        $this->setSubscription('basic');
        $result = $this->subscriptionService->downgrade($this->customer, 'basic');
        $this->assertFalse($result['success']);
    }

    // ========== API TESTS ==========

    #[Test]
    public function api_returns_subscription_plans(): void
    {
        $response = $this->actingAs($this->customer)
            ->getJson('/api/v1/subscriptions/plans');

        $response->assertOk()
            ->assertJsonCount(3, 'data');
    }

    #[Test]
    public function api_subscribe_requires_tier(): void
    {
        $response = $this->actingAs($this->customer)
            ->postJson('/api/v1/subscriptions/subscribe', []);

        $response->assertStatus(422);
    }

    #[Test]
    public function api_subscribe_with_valid_tier_succeeds(): void
    {
        $response = $this->actingAs($this->customer)
            ->postJson('/api/v1/subscriptions/subscribe', ['tier' => 'basic']);

        $response->assertCreated()
            ->assertJsonPath('success', true);
    }

    #[Test]
    public function api_subscription_status_returns_data(): void
    {
        $this->setSubscription('premium');
        $response = $this->actingAs($this->customer)
            ->getJson('/api/v1/subscriptions/status');

        $response->assertOk()
            ->assertJsonPath('data.tier', 'premium');
    }

    #[Test]
    public function api_calculate_checkout_returns_pricing(): void
    {
        $this->setSubscription('premium');
        $response = $this->actingAs($this->customer)
            ->postJson('/api/v1/checkout/calculate', [
                'subtotal' => 10000,
                'delivery_fee' => 1000,
                'items' => [
                    ['price' => 5000, 'quantity' => 2],
                ],
            ]);

        $response->assertOk()
            ->assertJsonPath('data.applied_tier', 'premium')
            ->assertJsonPath('data.delivery_fee', 0);
    }

    // ========== BILLING TESTS ==========

    #[Test]
    public function premium_gets_monthly_wallet_credit(): void
    {
        $this->setSubscription('premium');

        Wallet::firstOrCreate(
            ['user_id' => $this->customer->id],
            ['balance' => 0]
        );

        $this->billingService->creditPremiumWallet($this->customer);

        $wallet = $this->customer->wallet->fresh();
        $this->assertEquals(500, $wallet->balance);

        $log = WalletTransactionLog::where('user_id', $this->customer->id)->first();
        $this->assertNotNull($log);
        $this->assertEquals('subscription_credit', $log->type);
        $this->assertEquals(500, $log->amount);
    }

    #[Test]
    public function reset_period_benefits_clears_free_delivery_counter(): void
    {
        $this->setSubscription('basic');
        $this->customer->free_delivery_used = 1;
        $this->customer->save();

        $this->billingService->resetPeriodBenefits($this->customer);
        $this->customer->refresh();

        $this->assertEquals(0, $this->customer->free_delivery_used);
    }

    #[Test]
    public function calculate_checkout_returns_correct_values(): void
    {
        $this->setSubscription('premium');
        $chef = User::factory()->create(['status' => 'active']);
        $chef->assignRole('chef');

        $result = $this->subscriptionService->calculateCheckout(
            $this->customer,
            10000,
            1000,
            [
                ['price' => 5000, 'quantity' => 2, 'chef_id' => $chef->id],
            ]
        );

        $this->assertEquals(2000, $result['total_discount']);
        $this->assertEquals(8000, $result['final_subtotal']);
        $this->assertEquals(0, $result['delivery_fee']);
        $this->assertEquals(3, $result['order_priority']);
    }

    #[Test]
    public function non_subscriber_cannot_subscribe_to_invalid_tier(): void
    {
        $response = $this->actingAs($this->customer)
            ->postJson('/api/v1/subscriptions/subscribe', ['tier' => 'nonexistent']);

        $response->assertStatus(422);
    }

    #[Test]
    public function free_delivery_tracking_works_for_basic(): void
    {
        $this->setSubscription('basic');
        $this->assertEquals(0, $this->customer->free_delivery_used);

        $this->subscriptionService->useFreeDelivery($this->customer);
        $this->customer->refresh();
        $this->assertEquals(1, $this->customer->free_delivery_used);

        $this->subscriptionService->useFreeDelivery($this->customer);
        $this->customer->refresh();
        $this->assertEquals(2, $this->customer->free_delivery_used);
    }

    #[Test]
    public function calculate_prorated_upgrade_returns_non_negative(): void
    {
        $basic = SubscriptionPlan::where('slug', 'basic')->first();
        $premium = SubscriptionPlan::where('slug', 'premium')->first();

        $amount = $this->billingService->calculateProratedAmount($basic, $premium);
        $this->assertGreaterThanOrEqual(0, $amount);
    }

    // ========== HELPERS ==========

    protected function setSubscription(string $tier): void
    {
        $plan = SubscriptionPlan::where('slug', $tier)->firstOrFail();

        UserSubscription::create([
            'user_id' => $this->customer->id,
            'subscription_plan_id' => $plan->id,
            'status' => 'active',
            'billing_cycle' => 'monthly',
            'amount' => $plan->monthly_price,
            'current_period_start' => now(),
            'current_period_end' => now()->addMonth(),
        ]);

        $this->customer->update([
            'subscription_tier' => $tier,
            'subscription_status' => 'active',
        ]);
    }
}
