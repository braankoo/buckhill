<?php

namespace App\Http\Resources;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class PaymentResource extends JsonResource
{

    public function __construct(Payment $resource)
    {
        parent::__construct($resource);
    }

    public function toArray(Request $request): array
    {
        return [
            'type' => $this->resource->type,
            'details' => json_decode($this->resource->details),
            'created_at' => $this->resource->created_at,
        ];
    }
}
