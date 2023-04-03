<?php

namespace App\Http;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\LoginRequest;
use App\Http\Resources\AdminResource;
use App\Models\User;
use App\Services\UserAuthService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class BaseAuthController extends Controller
{
    private int $isAdmin;

    private UserAuthService $service;

    public function __construct(UserAuthService $service, int $isAdmin = 0)
    {
        $this->service = $service;
        $this->isAdmin = $isAdmin;
    }

    public function create(Requests\User\CreateRequest $request): JsonResponse
    {
        $attributes = $request->safe()->merge(['is_admin' => $this->isAdmin]
        )->all();
        try {
            $user = $this->service->create($attributes, AdminResource::class);
            return Response::api(HttpResponse::HTTP_OK, 1, $user);
        } catch (\ErrorException $e) {
            return Response::api(
                HttpResponse::HTTP_INTERNAL_SERVER_ERROR,
                0,
                ['error']
            );
        }
    }

    public function login(LoginRequest $request): JsonResponse
    {
        if (!Auth::attempt($request->safe()->only('email', 'password'))) {
            return $this->unauthorized();
        }
        $user = User::whereId(Auth::id())->firstOrFail();

        if ($user->is_admin !== $this->isAdmin) {
            return $this->unauthorized();
        }
        try {
            $token = $this->service->login($user);
        } catch (Exception $e) {
            return Response::api(
                HttpResponse::HTTP_INTERNAL_SERVER_ERROR,
                1,
                ['Error']
            );
        }
        return Response::api(
            HttpResponse::HTTP_OK,
            1,
            ['token' => $token->toString()]
        );
    }

    /**
     * @throws Exception
     */
    public function logout(Request $request): JsonResponse
    {
        if (!$token = $request->bearerToken()) {
            return Response::api(
                HttpResponse::HTTP_UNAUTHORIZED,
                0,
                [],
                'Token Required'
            );
        }
        try {
            $this->service->logout($token);
        } catch (\ErrorException $e) {
            return Response::api(
                HttpResponse::HTTP_INTERNAL_SERVER_ERROR,
                0,
                ['error']
            );
        }


        return Response::api(HttpResponse::HTTP_OK, 1, []);
    }

    /**
     * @return mixed
     */
    public function unauthorized():JsonResponse
    {
        return Response::api(
            HttpResponse::HTTP_UNAUTHORIZED,
            0,
            [],
            'Failed to authenticate user'
        );
    }
}
