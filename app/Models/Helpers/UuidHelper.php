<?php

namespace App\Models\Helpers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

final class UuidHelper
{
    public static function boot(Model $model): void
    {
        $model::creating(function (Model $model) {
            $model->setAttribute('uuid', Str::uuid()->toString());
        });
    }
}
