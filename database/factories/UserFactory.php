<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => fake()->uuid(),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'is_admin' => 0,
            'email' => fake()->safeEmail(),
            'password' => '$2y$10$wo1lL1/McWFAb6Hm9MLVFO4PE7IglgVTN2J/BFKaU5YHnhV8P0Hve', //userpassword
            'avatar' => null,
            'address' => fake()->address(),
            'phone_number' => fake()->phoneNumber(),
            'is_marketing' => 1,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function isAdmin(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_admin' => true,
        ]);
    }
}
