<?php

namespace App\Contracts;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

interface Pagination
{
    public function paginateRequest(Request $request, Builder $builder): LengthAwarePaginator;
}
