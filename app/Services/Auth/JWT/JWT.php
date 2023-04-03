<?php

namespace App\Services\Auth\JWT;

use App\Models\User;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\UnencryptedToken;

interface JWT
{
    public function provideToken(User $user): UnencryptedToken;

    public function parseToken(string $token): Token;
}
