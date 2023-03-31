<?php

namespace App\Http\Requests\Order;

use App\Rules\ProductRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
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
            'amount' => 'numeric|required'
        ];
    }
}
