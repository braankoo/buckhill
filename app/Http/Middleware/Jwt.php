<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

final class Jwt
{
    private $userLevel;

    public function handle(
        Request $request,
        Closure $next,
        string $userLevel = 'user'
    ): Response {
        $this->userLevel = $userLevel;
        $token = $request->bearerToken();
        if (!$token) {
            return $this->unauthorizedResponse();
        }

        $token = $this->parseToken($token);

        if ($this->isTokenExpired($token) || !$this->isUserLevelValid($token)) {
            return $this->unauthorizedResponse();
        }

        $userUuid = $token->claims()->get('user_uuid');
        $tokenId = $token->claims()->get('jti');
        $user = $this->getUserByUuidAndTokenId($userUuid, $tokenId);

        if (!$user) {
            return $this->unauthorizedResponse();
        }

        Auth::setUser($user);

        return $next($request);
    }

    private function isTokenExpired($token): bool
    {
        return $token->claims()->get('exp') < new \DateTimeImmutable();
    }

    private function isUserLevelValid($token): bool
    {
        return $token->claims()->get('user_level') === $this->userLevel;
    }

    private function parseToken($token)
    {
        try {
            return \App\Facades\Jwt::parseToken($token);
        } catch (\Exception $e) {
            return null;
        }
    }

    private function getUserByUuidAndTokenId($userUuid, $tokenId)
    {
        return User::where('uuid', '=', $userUuid)->hasToken($tokenId)->first();
    }

    private function unauthorizedResponse($message = 'Unauthorized')
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
        ], HttpResponse::HTTP_UNAUTHORIZED);
    }
}
