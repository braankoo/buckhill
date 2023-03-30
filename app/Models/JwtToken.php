<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class JwtToken extends Model
{
    use HasFactory;

    protected $fillable = ['unique_id','token_title', 'expires_at'];
}
