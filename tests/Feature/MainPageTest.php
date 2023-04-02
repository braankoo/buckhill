<?php

namespace Tests\Feature;

use App\Models\Post;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MainPageTest extends TestCase
{
    use DatabaseTransactions;
    use DatabaseMigrations;

    /**
     * A basic feature test example.
     */
    public function test_index(): void
    {
        $response = $this->get(route('main.promotion.index'));

        $response->assertStatus(200);
    }

    public function test_blog_index(): void
    {
        $response = $this->get(route('main.blog.index'));

        $response->assertStatus(200);
    }

    public function test_blog_single(): void
    {
        $post = Post::factory()->create();
        $response = $this->get(route('main.blog.single', ['post' => $post->uuid]));

        $response->assertStatus(200);
    }
}
