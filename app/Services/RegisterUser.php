<?php

namespace App\Services;

use App\Facades\Jwt;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class RegisterUser
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
            dd($e);
            Log::debug('Error while creating new regular user', [$e->getMessage(), $e->getTrace()]);
            return false;

        }
    }

}
