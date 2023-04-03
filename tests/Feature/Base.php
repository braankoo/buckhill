<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\UserAuthService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class Base extends TestCase
{
    use DatabaseTransactions;
    use DatabaseMigrations;

    protected function getAdminUser()
    {
        return User::factory()->create(['is_admin' => 1]);
    }

    protected function getRegularUser()
    {
        return User::factory()->create(['is_admin' => 0]);
    }

    protected function httpRequestWithToken(User $user): self
    {
        $token = app(UserAuthService::class)->login($user);

        return $this->withHeaders([
            'Authorization' => 'Bearer ' . $token->toString(),
            'Accept' => 'application/json',
        ]);
    }
}
