<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\User;
use App\Services\TokenService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BrandTest extends Base
{
    public function test_index(): void
    {
        $response = $this->get(route('brand.index'));

        $response->assertStatus(200);
    }

    public function test_post_admin_user()
    {
        $this->httpRequestWithToken(
            app(TokenService::class)->login($this->getAdminUser(), true)
        )->post(route('brand.store'), [
            'title' => fake()->word()
        ])->assertStatus(401);
    }

    public function test_show_admin_user()
    {
        $brand = Brand::factory()->create();

        $this->httpRequestWithToken(
            app(TokenService::class)->login($this->getAdminUser(), true)
        )->get(route('brand.show', ['brand' => $brand]))
            ->assertStatus(200)
            ->assertJson(['data' => ['uuid' => true]]);
    }

    public function test_post_regular_user()
    {
        $this->httpRequestWithToken(
            app(TokenService::class)->login($this->getRegularUser(), true)
        )->post(
            route('brand.store'),
            [
                'title' => fake()->word()
            ]
        )->assertJson(['data' => ['uuid' => true]])
            ->assertStatus(200);
    }

    public function test_put_admin_user()
    {
        $brand = Brand::factory()->create();

        $this->httpRequestWithToken(
            app(TokenService::class)->login($this->getAdminUser(), true)
        )->put(route('brand.update', $brand->uuid), [
        ])->assertStatus(401);
    }

    public function test_put_regular_user()
    {
        $brand = Brand::factory()->create();
        $this->httpRequestWithToken(
            app(TokenService::class)->login($this->getRegularUser(), true)
        )->put(
            route('brand.update', $brand->uuid),
            [
                'title' => fake()->word()
            ]
        )->assertStatus(200);
    }

    public function test_delete_regular_user()
    {
        $brand = Brand::factory()->create();
        $this->httpRequestWithToken(
            app(TokenService::class)->login($this->getRegularUser(), true)
        )->delete(
            route('brand.destroy', $brand->uuid)
        )->assertStatus(200);
        self::assertEquals(Brand::find($brand)->count(), 0);
    }
}
