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


    public function handle(Request $request, Closure $next, string $userLevel = 'user'): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return \Illuminate\Http\Response::api(HttpResponse::HTTP_UNAUTHORIZED, 1, [], 'Unauthorized');
        }


        try {
            $token = \App\Facades\Jwt::parseToken($token);
            $tokenId = $token->claims()->get('jti');
            $userUuid = $token->claims()->get('user_uuid');
            $userLevelToken = $token->claims()->get('user_level');

            if (
                $token->claims()->get('exp') < new \DateTimeImmutable()
                || $userLevelToken !== $userLevel

            ) {
                return \Illuminate\Http\Response::api(HttpResponse::HTTP_UNAUTHORIZED, 1, [], 'Unauthorized');
            }

            if (!$user = User::where('uuid', '=', $userUuid)->hasToken($tokenId)->first()) {
                return \Illuminate\Http\Response::api(HttpResponse::HTTP_UNAUTHORIZED, 1, [], 'Unauthorized');
            }

            Auth::setUser($user);
        } catch (\Exception $e) {
            return \Illuminate\Http\Response::api(HttpResponse::HTTP_UNAUTHORIZED, 1, [], 'Unauthorized');
        }

        return $next($request);
    }
}
