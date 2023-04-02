<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\File;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::each(function ($category) {
            Product::factory()->count(rand(1, 50))->create(
                [
                    'category_uuid' => $category->uuid,
                    'metadata' => json_encode([
                        'brand' => Brand::inRandomOrder()->first()->uuid,
                        'image' => File::inRandomOrder()->first()->uuid,
                    ]),
                ]
            );
        });
    }
}
