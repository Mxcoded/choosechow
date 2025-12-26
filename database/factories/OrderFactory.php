<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_number' => 'ORD-' . $this->faker->unique()->numerify('#####'),
            'customer_id' => \App\Models\User::factory(),
            'chef_id' => \App\Models\User::factory(),
            'status' => 'pending',
            'subtotal' => 5000,
            'delivery_fee' => 500,
            'service_fee' => 100,
            'total_amount' => 5600,
            'payment_method' => 'card',
            'payment_status' => 'paid',
            'delivery_address' => ['address' => $this->faker->address],
        ];
    }
}
