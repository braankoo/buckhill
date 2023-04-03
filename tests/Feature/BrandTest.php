<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Services\UserAuthService;

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
            $this->getAdminUser()
        )->post(route('brand.store'), [
            'title' => fake()->word(),
        ])->assertStatus(401);
    }

    public function test_show_admin_user()
    {
        $brand = Brand::factory()->create();

        $this->httpRequestWithToken(
            $this->getAdminUser()
        )->get(route('brand.show', ['brand' => $brand]))
            ->assertStatus(200)
            ->assertJson(['data' => ['uuid' => true]]);
    }

    public function test_post_regular_user()
    {
        $this->httpRequestWithToken(
            $this->getRegularUser()
        )->post(
            route('brand.store'),
            [
                'title' => fake()->word(),
            ]
        )->assertJson(['data' => ['uuid' => true]])
            ->assertStatus(200);
    }

    public function test_put_admin_user()
    {
        $brand = Brand::factory()->create();

        $this->httpRequestWithToken(
            $this->getAdminUser()
        )->put(route('brand.update', $brand->uuid), [
        ])->assertStatus(401);
    }

    public function test_put_regular_user()
    {
        $brand = Brand::factory()->create();
        $this->httpRequestWithToken(
            $this->getRegularUser()
        )->put(
            route('brand.update', $brand->uuid),
            [
                'title' => fake()->word(),
            ]
        )->assertStatus(200);
    }

    public function test_delete_regular_user()
    {
        $brand = Brand::factory()->create();
        $this->httpRequestWithToken(
            $this->getRegularUser()
        )->delete(
            route('brand.destroy', $brand->uuid)
        )->assertStatus(200);
        self::assertEquals(Brand::find($brand)->count(), 0);
    }
}
