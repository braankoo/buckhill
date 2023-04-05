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

    /**
     * @return array<string, array<string>|string>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->resource->uuid,
            'type' => $this->resource->type,
            'details' => json_decode($this->resource->details),
            'created_at' => $this->resource->created_at,
        ];
    }
}
