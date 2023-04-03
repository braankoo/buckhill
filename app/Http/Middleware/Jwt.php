<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

final class Jwt
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        if ( ! $request->bearerToken()) {
            return $this->unauthorizedResponse();
        }
        $token = \App\Facades\Jwt::parseToken($request->bearerToken());

        if (
            ! \App\Facades\Jwt::validateToken($token) ||
            $token->isExpired(new \DateTimeImmutable())
        ) {
            return $this->unauthorizedResponse();
        }

        return $next($request);
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
