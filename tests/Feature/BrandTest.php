<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\User;
use App\Services\TokenService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class BrandTest extends TestCase
{
    use DatabaseTransactions;
    use DatabaseMigrations;

    public function test_index(): void
    {
        $response = $this->get(route('brand.index'));

        $response->assertStatus(200);
    }

    public function test_post_admin_user()
    {
        $user = User::factory()->create(['is_admin' => 1]);
        $token = app(TokenService::class)->login($user, true);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token->toString(),
            'Accept' => 'application/json'
        ])->post(route('brand.store'), [
            'title' => fake()->word()
        ]);

        $response->assertStatus(401);
    }

    public function test_show_admin_user()
    {
        $user = User::factory()->create(['is_admin' => 1]);
        $token = app(TokenService::class)->login($user, true);
        $brand = Brand::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token->toString(),
            'Accept' => 'application/json'
        ])->get(route('brand.show', ['brand' => $brand]));

        $response->assertStatus(200);
        $response->assertJson(['data' => ['uuid' => true]]);
    }

    public function test_post_regular_user()
    {
        $user = User::factory()->create(['is_admin' => 0]);
        $token = app(TokenService::class)->login($user, true);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token->toString(),
            'Accept' => 'application/json'
        ])->post(route('brand.store'), [
            'title' => fake()->word()
        ])->assertJson(['data' => ['uuid' => true]]);

        $response->assertStatus(200);
    }

    public function test_put_admin_user()
    {
        $user = User::factory()->create(['is_admin' => 1]);
        $brand = Brand::factory()->create();
        $token = app(TokenService::class)->login($user, true);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token->toString(),
            'Accept' => 'application/json'
        ])->put(route('brand.update', $brand->uuid), [
        ]);

        $response->assertStatus(401);
    }

    public function test_put_regular_user()
    {
        $user = User::factory()->create(['is_admin' => 0]);
        $brand = Brand::factory()->create();
        $token = app(TokenService::class)->login($user, true);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token->toString(),
            'Accept' => 'application/json'
        ])->put(
            route('brand.update', $brand->uuid),
            [
                'title' => fake()->word()
            ]
        );

        $response->assertStatus(200);
    }

    public function test_delete_regular_user()
    {
        $user = User::factory()->create(['is_admin' => 0]);
        $brand = Brand::factory()->create();
        $token = app(TokenService::class)->login($user, true);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token->toString(),
            'Accept' => 'application/json'
        ])->delete(
            route('brand.destroy', $brand->uuid)
        );


        $response->assertStatus(200);
        self::assertEquals(Brand::find($brand)->count(), 0);
    }
}
