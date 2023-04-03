<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Lcobucci\JWT\UnencryptedToken;
use Symfony\Component\HttpFoundation\Response;

final class JwtAuth
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = \App\Facades\Jwt::parseToken($request->bearerToken());
        $user = $this->getUserFromToken($token);
        \Auth::setUser($user);
        return $next($request);
    }

    /**
     * @param UnencryptedToken $token
     * @return User|null
     */
    public function getUserFromToken(UnencryptedToken $token): ?User
    {
        $userUuid = $token->claims()->get('user_uuid');
        $tokenId = $token->claims()->get('jti');
        return $this->getUserByUuidAndTokenId($userUuid, $tokenId);

    }

    private function getUserByUuidAndTokenId(
        string $userUuid,
        int $tokenId
    ): User {
        return User::whereUuid($userUuid)->hasToken($tokenId)->firstOrFail();
    }
}
