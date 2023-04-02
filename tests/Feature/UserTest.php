<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\TokenService;

class UserTest extends Base
{
    /**
     * A basic feature test example.
     */
    public function test_index(): void
    {
        $response = $this->get(route('user.index'));

        $response->assertStatus(401);
    }

    public function test_index_admin_user(): void
    {
        $this->httpRequestWithToken(app(TokenService::class)->login($this->getAdminUser()))->get(
            route('user.index')
        )->assertStatus(401);
    }

    public function test_index_regular_user(): void
    {
        $this->httpRequestWithToken(app(TokenService::class)->login($this->getRegularUser()))->get(
            route('user.index')
        )->assertStatus(200);
    }

    public function test_login_regular(): void
    {
        $user = $this->getRegularUser();
        $this->post(
            route('user.login'),
            [
                'email' => $user->email,
                'password' => 'password'
            ]
        )->assertStatus(200);
    }

    public function test_login_admin(): void
    {
        $user = $this->getAdminUser();
        $response = $this->post(
            route('user.login'),
            [
                'email' => $user->email,
                'password' => 'password'
            ]
        );

        $response->assertStatus(401);
    }
}
