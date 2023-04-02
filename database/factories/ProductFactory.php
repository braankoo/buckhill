<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Category;
use App\Models\File;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
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
            'title' => fake()->sentence(4),
            'price' => fake()->randomFloat(2, 0, 300),
            'description' => fake()->text(100),
        ];
    }

    public function complete(): Factory
    {
        return $this->state(function ($attr) {
            $category = Category::factory()->create();
            $brand = Brand::factory()->create();
            $file = File::factory()->create();

            return array_merge(
                $attr,
                [
                    'category_uuid' => $category->uuid,
                    'metadata' => json_encode(
                        [
                            'brand' => $brand->uuid,
                            'image' => $file->uuid,
                        ]
                    ),
                ]
            );
        });
    }
}
