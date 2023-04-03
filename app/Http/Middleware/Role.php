<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

final class Role
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(Request): (HttpResponse) $next
     */
    public function handle(
        Request $request,
        Closure $next,
        string|false $role = false
    ): Response {
        $user = User::whereId(\Auth::id())->firstOrFail();
        if ($role === 'admin' && $user->is_admin) {
            return $next($request);
        }

        if ($role === 'user' && ! $user->is_admin) {
            return $next($request);
        }

        return $this->unauthorizedResponse();
    }

    private function unauthorizedResponse(
        string $message = 'Unauthorized'
    ): JsonResponse {
        return response()->json([
            'status' => 'error',
            'message' => $message,
        ], HttpResponse::HTTP_UNAUTHORIZED);
    }
}
