<?php

namespace App\Services;

use App\Contracts\Pagination;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

final class Paginator implements Pagination
{
    public function paginateRequest(Request $request, Builder $builder): LengthAwarePaginator
    {
        $perPage = $request->input('limit', 10);
        $sortBy = $request->input('sortBy', 'id');
        $page = $request->input('page', 1);

        if ($request->input('desc') === 'true') {
            $desc = 'DESC';
        } else {
            $desc = 'ASC';
        }

        return $builder->orderBy($sortBy, $desc)->paginate($perPage, ['*'], 'page', $page);
    }
}
