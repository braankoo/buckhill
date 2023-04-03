<?php

namespace App\Services;

use App\Facades\Jwt;
use App\Models\User;
use App\Rules\PaymentDetailsRule;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Lcobucci\JWT\Token;

final class TokenService
{
    /**
     * @param array<string, int|string> $attributes
     * @param class-string<JsonResource> $resource
     *
     * @return JsonResource|false
     */
    public function create(
        array $attributes,
        string $resource,
    ): JsonResource|false {
        try {
            DB::beginTransaction();
            $password = (string)$attributes['password'];
            $attributes['password'] = Hash::make($password);
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
            Log::debug(
                'Error while creating new regular user',
                [$e->getMessage(), $e->getTrace()]
            );

            return false;
        }
    }

    public function login(User $user): Token|false
    {
        try {
            DB::beginTransaction();
            $token = Jwt::provideToken($user);
            $user->tokens()->updateOrCreate(
                [
                    'unique_id' => $token->claims()->get('jti'),
                    'token_title' => 'access',
                    'user_id' => $user->id,
                ],
                [
                    'expires_at' => $token->claims()->get('exp'),
                ]
            );
            DB::commit();
        } catch (\Throwable $e) {
            Log::debug(
                'Error while logging in',
                [$e->getMessage(), $e->getTrace()]
            );

            return false;
        }

        return $token;
    }

    /**
     * @return array<int,int>
     */
    public function logout(Request $request): array
    {
        $token = Jwt::parseToken($request->bearerToken());

        $userUuid = $token->claims()->get('user_uuid');
        $tokenId = $token->claims()->get('jti');

        return [$userUuid, $tokenId];
    }
}
