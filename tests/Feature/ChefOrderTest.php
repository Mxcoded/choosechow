<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\Models\ChefProfile;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use PHPUnit\Framework\Attributes\Test;

class ChefOrderTest extends TestCase
{
    use RefreshDatabase;

    protected $chef;
    protected $otherChef;
    protected $customer;

    protected function setUp(): void
    {
        parent::setUp();

        // FIX: Disable CSRF protection for these tests to prevent 419 errors
        $this->withoutMiddleware([ValidateCsrfToken::class]);

        // 1. Setup Roles
        Role::firstOrCreate(['name' => 'chef']);
        Role::firstOrCreate(['name' => 'customer']);

        // 2. Create Users
        $this->chef = User::factory()->create(['status' => 'active']);
        $this->chef->assignRole('chef');

        // Create Chef Profile (Required for logic)
        ChefProfile::create([
            'user_id' => $this->chef->id,
            'business_name' => 'Chef A Kitchen',
            'slug' => 'chef-a-kitchen',
            'kitchen_address' => '123 Main St',
            'minimum_order' => 1000,
            'is_online' => true
        ]);

        $this->otherChef = User::factory()->create(['status' => 'active']);
        $this->otherChef->assignRole('chef');

        $this->customer = User::factory()->create(['status' => 'active']);
        $this->customer->assignRole('customer');
    }

    #[Test]
    public function chef_can_view_own_orders_but_not_others()
    {
        // Arrange: Create an order for Chef A
        $orderA = Order::create([
            'order_number' => 'ORD-001',
            'customer_id' => $this->customer->id,
            'chef_id' => $this->chef->id,
            'status' => 'pending',
            'subtotal' => 5000,
            'total_amount' => 5500,
            'delivery_address' => ['street' => 'Test St'],
            'payment_method' => 'card',
        ]);

        // Arrange: Create an order for Chef B
        $orderB = Order::create([
            'order_number' => 'ORD-002',
            'customer_id' => $this->customer->id,
            'chef_id' => $this->otherChef->id,
            'status' => 'pending',
            'subtotal' => 3000,
            'total_amount' => 3500,
            'delivery_address' => ['street' => 'Test St'],
            'payment_method' => 'card',
        ]);

        // Act & Assert: Chef A should see Order A but NOT Order B
        $response = $this->actingAs($this->chef)
            ->get(route('chef.orders.index'));

        $response->assertStatus(200);
        $response->assertSee('ORD-001');
        $response->assertDontSee('ORD-002');
    }

    #[Test]
    public function chef_can_update_order_status_to_confirmed()
    {
        // Arrange
        $order = Order::create([
            'order_number' => 'ORD-CONFIRM',
            'customer_id' => $this->customer->id,
            'chef_id' => $this->chef->id,
            'status' => 'pending',
            'subtotal' => 5000,
            'total_amount' => 5000,
            'delivery_address' => ['street' => 'Test St'],
            'payment_method' => 'card',
        ]);

        // Act: Hit the endpoint to confirm
        $response = $this->actingAs($this->chef)
            ->patchJson(route('chef.orders.update-status', $order), [
                'status' => 'confirmed'
            ]);

        // Assert
        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        // Verify Database
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'confirmed'
        ]);

        // Verify Timestamp Helper worked
        $updatedOrder = $order->fresh();
        $this->assertNotNull($updatedOrder->confirmed_at);
    }

    #[Test]
    public function chef_cannot_update_another_chefs_order()
    {
        // Arrange: Order belongs to OTHER Chef
        $order = Order::create([
            'order_number' => 'ORD-THEFT',
            'customer_id' => $this->customer->id,
            'chef_id' => $this->otherChef->id,
            'status' => 'pending',
            'subtotal' => 5000,
            'total_amount' => 5000,
            'delivery_address' => ['street' => 'Test St'],
            'payment_method' => 'card',
        ]);

        // Act: Chef A tries to update Chef B's order
        $response = $this->actingAs($this->chef)
            ->patchJson(route('chef.orders.update-status', $order), [
                'status' => 'confirmed'
            ]);

        // Assert: Should be Forbidden (403)
        $response->assertStatus(403);
    }

    #[Test]
    public function it_calculates_chef_earnings_correctly()
    {
        // Arrange
        $order = new Order([
            'subtotal' => 10000,
            'delivery_fee' => 1000,
            'service_fee' => 500,
            'total_amount' => 11500
        ]);

        // Act
        $earnings = $order->getChefEarnings();

        // Assert
        $this->assertEquals(10000, $earnings);
    }

    #[Test]
    public function order_index_shows_correct_statistics()
    {
        // Arrange: Create 3 Pending, 1 Delivered
        Order::factory()->count(3)->create([
            'chef_id' => $this->chef->id,
            'customer_id' => $this->customer->id,
            'status' => 'pending',
            'subtotal' => 100,
            'total_amount' => 100,
            'payment_method' => 'card',
            'delivery_address' => []
        ]);

        Order::factory()->create([
            'chef_id' => $this->chef->id,
            'customer_id' => $this->customer->id,
            'status' => 'delivered',
            'subtotal' => 100,
            'total_amount' => 100,
            'payment_method' => 'card',
            'delivery_address' => []
        ]);

        // Act
        $response = $this->actingAs($this->chef)->get(route('chef.orders.index'));

        // Assert: Check if the stats array in the view has correct numbers
        $response->assertViewHas('stats', function ($stats) {
            return $stats['pending'] === 3 && $stats['delivered'] === 1 && $stats['all'] === 4;
        });
    }
}
