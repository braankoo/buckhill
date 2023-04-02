<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Services\TokenService;

class CategoryTest extends Base
{
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
        $category = Category::factory()->create();
        $this->httpRequestWithToken(
            app(TokenService::class)->login($this->getAdminUser(), true)
        )->put(
            route(
                'category.update',
                [
                    'category' => $category->uuid,
                ]
            ),
            ['category' => $category->uuid]
        )->assertStatus(401);
    }

    public function test_put_regular_user()
    {
        $category = Category::factory()->create();
        $this->httpRequestWithToken(
            app(TokenService::class)->login($this->getRegularUser(), true)
        )->put(
            route(
                'category.update',
                [
                    'category' => $category->uuid,
                ]
            ),
            [
                'title' => '123123123',
            ]
        )->assertStatus(200);
        self::assertEquals(Category::find($category)->first()->title, '123123123');
    }
}
