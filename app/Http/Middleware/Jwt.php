<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Lcobucci\JWT\UnencryptedToken;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

final class Jwt
{
    private string $accessLevel;

    public function handle(
        Request $request,
        Closure $next,
        string $accessLevel = 'user'
    ): Response {
        $this->accessLevel = $accessLevel;

        if (!$request->bearerToken()) {
            return $this->unauthorizedResponse();
        }

        $token = $this->parseToken($request->bearerToken());


        if (is_null($token) || $this->isTokenExpired($token) || !$this->isAccessLevelValid($token)) {
            return $this->unauthorizedResponse();
        }


        $userUuid = $token->claims()->get('user_uuid');
        $tokenId = $token->claims()->get('jti');
        $user = $this->getUserByUuidAndTokenId($userUuid, $tokenId);

        if (is_null($user)) {
            return $this->unauthorizedResponse();
        }

        Auth::setUser($user);

        return $next($request);
    }

    private function isTokenExpired(UnencryptedToken $token): bool
    {
        return $token->claims()->get('exp') < new \DateTimeImmutable();
    }

    private function isAccessLevelValid(UnencryptedToken $token): bool
    {
        return $token->claims()->get('access_level') === $this->accessLevel;
    }

    private function parseToken(string $token): ?UnencryptedToken
    {
        try {
            return \App\Facades\Jwt::parseToken($token);
        } catch (\Exception $e) {
            return null;
        }
    }

    private function getUserByUuidAndTokenId(
        string $userUuid,
        int $tokenId
    ): User|null {
        return User::where('uuid', '=', $userUuid)->hasToken($tokenId)->first();
    }

    private function unauthorizedResponse(string $message = 'Unauthorized'
    ): JsonResponse {
        return response()->json([
            'status' => 'error',
            'message' => $message,
        ], HttpResponse::HTTP_UNAUTHORIZED);
    }
}
