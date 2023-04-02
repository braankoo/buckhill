<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Services\TokenService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use DatabaseTransactions;
    use DatabaseMigrations;

    public function test_index(): void
    {
        $response = $this->get(route('category.index'));

        $response->assertStatus(200);
    }

    public function test_show_single()
    {
        $category = Category::factory()->create();
        $response = $this->get(route('category.show', ['category' => $category->uuid]));
        $response->assertStatus(200);
    }

    public function test_put_admin_user()
    {
        $user = User::factory()->create(['is_admin' => 1]);
        $category = Category::factory()->create();
        $token = app(TokenService::class)->login($user, true);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token->toString(),
            'Accept' => 'application/json'
        ])->put(
            route(
                'category.update',
                [
                    'category' => $category->uuid
                ]
            ),
            ['category' => $category->uuid]
        );
        $response->assertStatus(401);
    }

    public function test_put_regular_user()
    {
        $user = User::factory()->create(['is_admin' => 0]);
        $category = Category::factory()->create();
        $token = app(TokenService::class)->login($user, true);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token->toString(),
            'Accept' => 'application/json'
        ])->put(
            route(
                'category.update',
                [
                    'category' => $category->uuid
                ]
            ),
            [
                'title' => '123123123'
            ]
        );
        $response->assertStatus(200);
        self::assertEquals(Category::where('id', '=', $category->id)->first()->title, '123123123');
    }
}
