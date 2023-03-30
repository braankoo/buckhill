<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\IndexRequest;
use App\Http\Requests\Admin\User\UpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\Paginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

/**
 *
 */
final class UserController extends Controller
{

    public function index(IndexRequest $request, Paginator $paginator): LengthAwarePaginator
    {
        $query = User::where('is_admin', '=', 0);
        $searchableFields = ['first_name', 'email', 'phone', 'address', 'created_at', 'is_marketing'];
        foreach ($searchableFields as $field) {
            if ($request->has($field)) {
                $query->where($field, '=', $request->input($field));
            }
        }

        return $paginator->paginateRequest($request, $query);
    }

    public function update(User $user, UpdateRequest $request): JsonResponse
    {
        $attributes = $request->safe()->all();
        if (is_null($attributes['is_marketing'])) {
            unset($attributes['is_marketing']);
        }
        $user->update($attributes);

        return Response::api(HttpResponse::HTTP_OK, 1, new UserResource($user));
    }

    public function destroy(User $user): JsonResponse
    {
        $user->delete();
        return Response::api(HttpResponse::HTTP_OK, 1, []);
    }
}
