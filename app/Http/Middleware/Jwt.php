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
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return \Illuminate\Http\Response::api(HttpResponse::HTTP_UNAUTHORIZED, 1, [], 'Unauthorized');
        }

        try {
            $token = \App\Facades\Jwt::parseToken($token);
        } catch (\Exception $e) {
            return \Illuminate\Http\Response::api(HttpResponse::HTTP_UNAUTHORIZED, 1, [], 'Unauthorized');
        }

        if ($token->claims()->get('exp') < new \DateTimeImmutable()) {
            return \Illuminate\Http\Response::api(HttpResponse::HTTP_UNAUTHORIZED, 1, [], 'Unauthorized');
        }

        $userUuid = $token->claims()->get('user_uuid');
        Auth::setUser(User::where('uuid', '=', $userUuid)->first());

        return $next($request);
    }
}
