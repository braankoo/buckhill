<?php

namespace App\Models;

use App\Models\Helpers\UuidHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Payment extends Model
{
    use HasFactory;

    protected $fillable = ['type', 'details'];


    public function getRouteKeyName()
    {
        return 'uuid';
    }

    protected static function boot()
    {
        parent::boot();
        UuidHelper::boot(new static());
    }
}
