<?php

namespace App\Http\Resources;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class ProductResource extends JsonResource
{

    public function __construct(Product $resource)
    {
        parent::__construct($resource);
    }

    public function toArray(Request $request): array
    {
        return [
            'category_uuid' => $this->resource->category_uuid,
            'title' => $this->resource->title,
            'price' => $this->resource->price,
            'metadata' => json_decode($this->resource->metadata, true),
            'uuid' => $this->resource->uuid,
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at,
        ];
    }
}
