<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Database\Factories\OrderFactory;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::where('is_admin', '=', 0)->each(function ($user) {
            Order::factory()
                ->count(rand(50, 500))
                ->create(
                    [
                        'user_id' => $user->id,
                        'products' => json_encode(
                            Product::take(rand(1, 20))->get()->map(function ($product) {
                                return ['product' => $product->uuid, 'quantity' => rand(1, 10)];
                            })->toArray()
                        )
                    ]
                );
        });
    }
}
