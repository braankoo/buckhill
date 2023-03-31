<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'products' => json_decode($this->products),
            'address' => json_decode($this->address),
            'amount' => $this->amount,
            'uuid' => $this->uuid
        ];
    }
}
