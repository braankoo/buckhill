<?php

namespace App\Services\Auth\JWT;

use Illuminate\Contracts\Auth\Authenticatable;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\UnencryptedToken;

interface JWT
{
    public function provideToken(Authenticatable $user): UnencryptedToken;

    public function parseToken(string $token): Token;
}
