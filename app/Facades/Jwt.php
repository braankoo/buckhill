<?php

namespace App\Facades;

use App\Services\Auth\JWT\JWT as JWTInterface;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Facade;
use Lcobucci\JWT\UnencryptedToken;

/**
 * @method static UnencryptedToken provideToken(Authenticatable $user)
 * @method static UnencryptedToken parseToken(string $token)
 */
final class Jwt extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return JWTInterface::class;
    }
}
