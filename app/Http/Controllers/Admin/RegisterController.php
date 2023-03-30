<?php

namespace App\Http\Controllers\Admin;

use App\Facades\Jwt;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateRequest;
use App\Http\Resources\AdminResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

/**
 *
 */
final class RegisterController extends Controller
{

    public function create(CreateRequest $request): JsonResponse
    {
        $attributes = $request->safe()->merge(['is_admin' => true])->all();
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

            $resource = (new AdminResource($user));
            $resource->additional(['token' => $token->toString()]);

            return Response::api(HttpResponse::HTTP_CREATED, 1, $resource);
        } catch (\Exception $e) {
            Log::debug('Error while creating new admin user', [$e->getMessage(), $e->getTrace()]);
            return Response::api(HttpResponse::HTTP_INTERNAL_SERVER_ERROR, 0, []);
        }
    }
}
