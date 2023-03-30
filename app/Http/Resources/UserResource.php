<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at,
            'avatar' => $this->avatar,
            'is_marketing' => $this->is_marketing,
            'address' => $this->address,
            'phone_number' => $this->phone_number,
            'updated_at' => $this->updated_at,
            'created_at' => $this->created_at,
            'last_login' => $this->last_login,
        ];
    }
}
