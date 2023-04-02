<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\TokenService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Lcobucci\JWT\Token;
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

    protected function httpRequestWithToken(Token $token): Base
    {
        return $this->withHeaders([
            'Authorization' => 'Bearer ' . $token->toString(),
            'Accept' => 'application/json'
        ]);
    }


}
