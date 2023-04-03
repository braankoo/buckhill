<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

final class CreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<string>|string>
     */
    public function rules(): array
    {
        return [
            'order_status_uuid' => 'required|uuid|exists:order_statuses,uuid',
            'payment_uuid' => 'required|uuid|exists:payments,uuid',
            'products' => ['required'],
            'products.*.product' => 'uuid|exists:products,uuid',
            'products.*.quantity' => 'integer',
            'address.billing' => 'required|string',
            'address.shipping' => 'required|string',
            'amount' => 'numeric|required',
        ];
    }
}
