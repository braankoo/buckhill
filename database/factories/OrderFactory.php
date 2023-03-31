<?php

namespace Database\Factories;

use App\Models\OrderStatus;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

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
            'uuid' => Str::uuid(),
            'payment_id' => Payment::factory(),
            'order_status_id' => OrderStatus::factory(),
            'address' => json_encode(
                [
                    "billing" => fake()->address(),
                    "shipping" => fake()->address()
                ]
            ),
            'delivery_fee' => fake()->randomFloat(2, 5, 20),
            'amount' => fake()->randomFloat(3, 4, 5000)
        ];
    }
}
