<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class File extends Model
{
    use HasFactory;

    protected $fillable = ['uuid', 'name', 'path', 'size', 'type'];

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
