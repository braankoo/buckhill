<?php

namespace App\Services;

use App\Facades\Jwt;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Lcobucci\JWT\Token;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class UserAuthService
{

    public function create(Request $request, string $resource, bool $isAdmin): JsonResource|false
    {
        $attributes = $request->safe()->merge(['is_admin' => $isAdmin])->all();
        $attributes['password'] = Hash::make($attributes['password']);
        try {
            DB::beginTransaction();

            $user = User::create($attributes);
            $token = Jwt::provideToken($user);

            $user->tokens()->create([
                'unique_id' => $token->claims()->get('jti'),
                'token_title' => 'access',
                'expires_at' => $token->claims()->get('exp'),
            ]);

            DB::commit();
            $resource = (new $resource($user));
            $resource->additional(['token' => $token->toString()]);
            return $resource;
        } catch (\Throwable $e) {
            Log::debug('Error while creating new regular user', [$e->getMessage(), $e->getTrace()]);
            return false;
        }
    }

    public function login(Request $request): Token|false
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            return Jwt::provideToken($user);
        }
        return false;
    }

    public function logout(Request $request): array
    {
        $token = Jwt::parseToken($request->bearerToken());

        $userUuid = $token->claims()->get('user_uuid');
        $tokenId = $token->claims()->get('jti');
        return [$userUuid, $tokenId];
    }

}
