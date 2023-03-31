<?php

namespace App\Models;

use App\Models\Helpers\UuidHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

final class Brand extends Model
{
    use HasFactory;

    protected $fillable = ['title'];


    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    protected static function boot()
    {
        parent::boot();

        UuidHelper::boot(new static());

        self::creating(function (Model $model) {
            $model->setAttribute('slug', Str::slug($model->title));
        });

        self::updating(function (Model $model) {
            $model->setAttribute('slug', Str::slug($model->title));
        });
    }
}
