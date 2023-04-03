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
use Lcobucci\JWT\UnencryptedToken;

final class UserAuthService
{
    /**
     * @param array<string, int|string> $attributes
     * @param class-string<JsonResource> $resource
     *
     * @return JsonResource
     * @throws \ErrorException
     */
    public function create(
        array $attributes,
        string $resource,
    ): JsonResource {
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
            throw new \ErrorException('Error occured');
        }
    }

    /**
     * @throws \Exception|\Throwable
     */
    public function login(User $user): Token
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
            return $token;
        } catch (\Throwable $e) {
            Log::debug(
                'Error while logging in',
                [$e->getMessage(), $e->getTrace()]
            );
            DB::rollBack();
            throw new \ErrorException('Error');
        }
    }

    public function logout(string $token): bool
    {
        $token = Jwt::parseToken($token);
        $userUuid = $token->claims()->get('user_uuid');
        $tokenId = $token->claims()->get('jti');
        $user = User::whereUuid($userUuid)->firstOrFail();
        try {
            DB::beginTransaction();
            $user->tokens()->where('unique_id', '=', $tokenId)->delete();
            DB::commit();
            return true;
        } catch (\Throwable $e) {

            Log::debug(
                'Error while logging out',
                [$e->getMessage(), $e->getTrace()]
            );
            DB::rollBack();
            throw new \ErrorException('Error');
        }
    }

}
