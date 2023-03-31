<?php

namespace App\Models;

use App\Models\Helpers\UuidHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Post extends Model
{
    use HasFactory;


    public function getRouteKey()
    {
        return 'uuid';
    }

    protected static function boot()
    {
        parent::boot();
        UuidHelper::boot(new static());
    }
}
