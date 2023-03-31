<?php

namespace App\Models;

use App\Models\Helpers\UuidHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 *
 */
final class Order extends Model
{

    use HasFactory;

    protected $fillable = ['order_status_id', 'payment_id', 'products', 'address', 'amount'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function status(): HasOne
    {
        return $this->hasOne(OrderStatus::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

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
