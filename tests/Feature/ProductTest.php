<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\File;
use App\Models\Product;
use App\Services\TokenService;

class ProductTest extends Base
{
    public function test_index(): void
    {
        $response = $this->get(route('product.index'));

        $response->assertStatus(401);
    }

    public function test_show_single()
    {
        $product = Product::factory()->complete()->create();
        $this->httpRequestWithToken(
            app(TokenService::class)->login($this->getRegularUser(), true)
        )->get(route('product.show', ['product' => $product->uuid]))
            ->assertStatus(200);
    }

    public function test_put_admin_user()
    {
        $product = Product::factory()->complete()->create();
        $this->httpRequestWithToken(
            app(TokenService::class)->login($this->getAdminUser(), true)
        )->get(route('product.show', ['product' => $product->uuid]))
            ->assertStatus(401);
    }

    public function test_put_regular_user()
    {
        $product = Product::factory()->complete()->create();
        $category = Category::factory()->create();
        $brand = Brand::factory()->create();
        $file = File::factory()->create();


        $this->httpRequestWithToken(
            app(TokenService::class)->login($this->getRegularUser(), true)
        )->put(
            route(
                'product.update',
                [
                    'product' => $product->uuid,
                ]
            ),
            [
                'category_uuid' => $category->uuid,
                'price' => 123,
                'description' => 'test',
                'title' => '123123123',
                'metadata' => json_encode([
                    "brand" => $brand->uuid,
                    "image" => $file->uuid
                ])
            ]
        )->assertStatus(200);
        self::assertEquals('123123123', Product::find($product)->first()->title);
    }

    public function test_destroy_regular_user()
    {
        $product = Product::factory()->complete()->create();


        $this->httpRequestWithToken(
            app(TokenService::class)->login($this->getRegularUser(), true)
        )->delete(
            route(
                'product.destroy',
                [
                    'product' => $product->uuid,
                ]
            )
        )->assertStatus(200);;
        self::assertEquals(0, Product::find($product)->count());
    }
}
