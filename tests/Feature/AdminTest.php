<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\UserAuthService;

class AdminTest extends Base
{
    public function test_create(): void
    {
        $response = $this->post(route('admin.create'), [
            'first_name' => fake()->name(),
            'last_name' => fake()->lastName(),
            'password' => \Hash::make('password'),
            'email' => fake()->safeEmail(),
            'address' => fake()->address(),
            'phone_number' => fake()->phoneNumber(),
        ]);

        $response->assertJson(['data' => ['token' => true]]);

        $response->assertStatus(200);
    }

    public function test_login()
    {
        $user = $this->getAdminUser();

        $response = $this->post(route('admin.login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);
        $response->assertJson(['data' => ['token' => true]]);

        $response->assertStatus(200);
    }

    public function test_login_regular_user()
    {
        $user = $this->getRegularUser();
        $response = $this->post(route('admin.login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(401);
    }

    public function test_user_listing_with_admin_user()
    {

        $this->httpRequestWithToken($this->getAdminUser())
        ->get(route('admin.user.index'))->assertStatus(200);
    }

    public function test_user_listing_with_regular_user()
    {
        $this->httpRequestWithToken(
            $this->getRegularUser()
        )->get(route('admin.user.index'))->assertStatus(401);
    }

    public function test_user_to_update_with_admin()
    {
        $user = $this->getAdminUser();

        $response = $this->httpRequestWithToken(
            $user
        )->put(
            route('admin.user.update', [
                'user' => $user->uuid,
            ]),
            [
                'first_name' => 'name',
                'last_name' => 'last',
                'password' => '123123123123',
                'address' => '123123123123',
                'phone_number' => '123123',
                'email' => 'test@asdsd.comc'
            ]
        );
        $response = json_decode($response->getContent(), true);
        self::assertNotEquals($user->first_name, $response['data']['first_name']);
    }
}
