<?php

namespace Database\Factories;

use App\Models\File;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Promotion>
 */
class PromotionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => \Str::uuid(),
            'title' => fake()->words(2, true),
            'content' => fake()->text(),
            'metadata' => json_encode(
                [
                    'valid_form' => Carbon::now()->subDays(rand(1, 60))->format('Y-m-d'),
                    'valid_to' => Carbon::now()->format('Y-m-d'),
                    'image' => File::inRandomOrder()->first()->uuid
                ]
            )
        ];
    }
}
