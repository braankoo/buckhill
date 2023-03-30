<?php

namespace App\Http\Controllers\Admin;

use App\Facades\Jwt;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

final class LoginController extends Controller
{

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = Jwt::provideToken($user)->toString();

            return Response::api(HttpResponse::HTTP_OK, 1, ['token' => $token]);
        }

        return Response::api(HttpResponse::HTTP_UNPROCESSABLE_ENTITY, 0, [], 'Failed to authenticate user');
    }

    public function logout(Request $request): JsonResponse
    {
        $token = Jwt::parseToken($request->bearerToken());

        $userUuid = $token->claims()->get('user_uuid');
        $tokenId = $token->claims()->get('jti');

        User::where('uuid', '=', $userUuid)->first()->tokens()->where('unique_id', '=', $tokenId)->delete();
        return Response::api(HttpResponse::HTTP_OK, 0, [], 'Failed to authenticate user');

    }
}
