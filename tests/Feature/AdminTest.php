<?php

namespace Tests\Feature;

use App\Facades\Jwt;
use App\Models\User;
use App\Services\TokenService;
use App\Services\UserAuthService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use DatabaseTransactions;
    use DatabaseMigrations;

    /**
     * A basic feature test example.
     */
    public function test_create(): void
    {
        $response = $this->post(route('admin.create'), [
            'first_name' => fake()->name(),
            'last_name' => fake()->lastName(),
            'password' => \Hash::make('password'),
            'email' => fake()->safeEmail(),
            'address' => fake()->address(),
            'phone_number' => fake()->phoneNumber()
        ]);

        $response->assertJson(['data' => ['token' => true]]);

        $response->assertStatus(200);
    }

    public function test_login()
    {
        $user = User::factory()->create(['is_admin' => 1]);

        $response = $this->post(route('admin.login'), [
            'email' => $user->email,
            'password' => 'password'
        ]);
        $response->assertJson(['data' => ['token' => true]]);

        $response->assertStatus(200);
    }

    public function test_login_regular_user()
    {
        $user = User::factory()->create(['is_admin' => 0]);
        $response = $this->post(route('admin.login'), [
            'email' => $user->email,
            'password' => 'password'
        ]);

        $response->assertStatus(401);
    }

    public function test_user_listing_with_admin_user()
    {
        $user = User::factory()->create(['is_admin' => 1]);
        $token = app(TokenService::class)->login($user, true);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token->toString(),
            'Accept' => 'application/json'
        ])->get(route('admin.user.index'));


        $response->assertStatus(200);
    }

    public function test_user_listing_with_regular_user()
    {
        $user = User::factory()->create(['is_admin' => 0]);
        $token = app(TokenService::class)->login($user, true);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token->toString(),
            'Accept' => 'application/json'
        ])->get(route('admin.user.index'));


        $response->assertStatus(401);
    }

    public function test_user_to_update_with_admin()
    {
        $user = User::factory()->create(['is_admin' => 1]);
        $userToUpdate = User::factory()->create(['is_admin' => 0]);
        $token = app(TokenService::class)->login($user, true);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token->toString(),
            'Accept' => 'application/json'
        ])->put(
            route('admin.user.update', [
                'user' => $userToUpdate->uuid
            ]),
            [
                'first_name' => 'name',
                'last_name' => 'last',
                'password' => '123123123123',
                'address' => '123123123123',
                'phone_number' => '123123'
            ]
        );
        $response = json_decode($response->getContent(), true);
        self::assertNotEquals($userToUpdate->first_name, $response['data']['first_name']);
    }
}
