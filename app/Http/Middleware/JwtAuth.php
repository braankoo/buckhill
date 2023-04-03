<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Lcobucci\JWT\UnencryptedToken;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

final class JwtAuth
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();
        if ( ! $token) {
            return $this->unauthorizedResponse();
        }
        $token = \App\Facades\Jwt::parseToken($token);
        $user = $this->getUserFromToken($token);
        \Auth::setUser($user);

        return $next($request);
    }

    public function getUserFromToken(UnencryptedToken $token): Authenticatable
    {
        $userUuid = $token->claims()->get('user_uuid');
        $tokenId = $token->claims()->get('jti');

        return $this->getUserByUuidAndTokenId($userUuid, $tokenId);
    }

    private function getUserByUuidAndTokenId(
        string $userUuid,
        int $tokenId
    ): Authenticatable {
        return User::whereUuid($userUuid)->hasToken($tokenId)->firstOrFail();
    }

    private function unauthorizedResponse(
        string $message = 'Unauthorized'
    ): JsonResponse {
        return response()->json([
            'status' => 'error',
            'message' => $message,
        ], HttpResponse::HTTP_UNAUTHORIZED);
    }
}
