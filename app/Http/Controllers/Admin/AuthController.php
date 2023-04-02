<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateRequest;
use App\Http\Requests\Admin\LoginRequest;
use App\Http\Resources\AdminResource;
use App\Models\User;
use App\Services\TokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Throwable;

final class AuthController extends Controller
{
    private TokenService $tokenService;

    public function __construct(TokenService $tokenService)
    {
        $this->tokenService = $tokenService;
    }

    /**
     * @OA\Post(
     *     path="/api/v1/admin/create",
     *     tags={"Admin"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="first_name",
     *                     type="string",
     *                     description="User First Name",
     *                 ),
     *                 @OA\Property(
     *                     property="last_name",
     *                     type="string",
     *                     description="User First Name",
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     type="string",
     *                     description="User email",
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string",
     *                     description="User password",
     *                 ),
     *                @OA\Property(
     *                     property="avatar",
     *                     type="string",
     *                     description="User password",
     *                 ),
     *               @OA\Property(
     *                     property="address",
     *                     type="string",
     *                     description="User address",
     *                 ),
     *               @OA\Property(
     *                     property="phone_number",
     *                     type="string",
     *                     description="User phone number",
     *                 ),
     *               @OA\Property(
     *                     property="is_marketing",
     *                     description="User marketing preferences",
     *                     type="string",
     *                     enum={"0", "1"},
     *                 ),
     *                 required={"first_name", "last_name", "email","password","address","phone_number"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="OK"
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Page not found"
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Unprocessable Entity"
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Internal server error"
     *     )
     * )
     *
     * @throws Throwable
     */
    public function create(CreateRequest $request): JsonResponse
    {
        $attributes = $request->safe()->merge(['is_admin' => 0])->all();
        $user = $this->tokenService->create($attributes, AdminResource::class);
        if (!$user) {
            return Response::api(HttpResponse::HTTP_INTERNAL_SERVER_ERROR, 0, [], 'Error happend');
        }

        return Response::api(HttpResponse::HTTP_OK, 1, $user);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/admin/login",
     *     tags={"Admin"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="email",
     *                     type="string",
     *                     description="Admin email",
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string",
     *                     description="Admin password",
     *                 ),
     *                 required={"email", "password"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="OK"
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Page not found"
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Unprocessable Entity"
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Internal server error"
     *     )
     * )
     */
    public function login(LoginRequest $request): JsonResponse
    {
        if (!Auth::attempt($request->safe()->only('email', 'password')) || !Auth::user() || !Auth::user()->is_admin) {
            return Response::api(HttpResponse::HTTP_UNAUTHORIZED, 0, [], 'Failed to authenticate user');
        }
        $token = $this->tokenService->login(Auth::user());

        if (!$token) {
            return Response::api(HttpResponse::HTTP_UNAUTHORIZED, 0, [], 'Failed to authenticate user');
        }

        return Response::api(HttpResponse::HTTP_OK, 1, ['token' => $token->toString()]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/admin/logout",
     *     tags={"Admin"},
     *     @OA\Response(
     *         response="200",
     *         description="OK"
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Page not found"
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Unprocessable Entity"
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Internal server error"
     *     )
     * )
     */
    public function logout(Request $request): JsonResponse
    {
        [$userUuid, $tokenId] = $this->tokenService->logout($request);

        User::where('uuid', '=', $userUuid)->first()->tokens()->where('unique_id', '=', $tokenId)->delete();

        return Response::api(HttpResponse::HTTP_OK, 0, [], 'Failed to authenticate user');
    }
}
