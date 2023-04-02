<?php

namespace App\Http\Resources;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class OrderResource extends JsonResource
{
    public function __construct(Order $resource)
    {
        parent::__construct($resource);
    }

    public function toArray(Request $request): array
    {
        return [
            'products' => json_decode($this->resource->products),
            'address' => json_decode($this->resource->address),
            'amount' => $this->resource->amount,
            'uuid' => $this->resource->uuid,
        ];
    }
}
