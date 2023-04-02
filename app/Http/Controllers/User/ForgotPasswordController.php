<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

final class ForgotPasswordController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/v1/user/forgot-password",
     *     tags={"User"},
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="User email",
     *         required=true,
     *         @OA\Schema(type="string")
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
    public function createResetToken(Request $request): JsonResponse
    {
        $this->validate($request, [
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();
        $token = Password::createToken($user);

        return Response::api(ResponseAlias::HTTP_OK, 1, ['token' => $token]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/user/reset-password-token",
     *     tags={"User"},
     *     @OA\Parameter(
     *         name="token",
     *         in="query",
     *         description="User reset token",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="User email",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         description="User password",
     *         required=true,
     *         @OA\Schema(type="string")
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
    public function resetPassword(Request $request): JsonResponse
    {
        $this->validate($request, [
            'email' => 'required|email|exists:users,email',
            'token' => 'required',
            'password' => 'required|min:8',
        ]);
        $response = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        );

        if ($response === Password::PASSWORD_RESET) {
            return Response::api(ResponseAlias::HTTP_OK, 1, ['message' => 'Password has been successfully updated']);
        }
        if ($response === Password::INVALID_TOKEN) {
            return Response::api(ResponseAlias::HTTP_OK, 1, ['message' => 'Invalid or expired token']);
        }

        return Response::api(ResponseAlias::HTTP_BAD_REQUEST, 0, ['message' => 'Unable to reset password.']);
    }
}
