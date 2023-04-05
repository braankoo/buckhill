<?php

namespace App\Http\Requests\Order;

use App\Rules\JsonRule;
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
     * @return array<string, array<int, JsonRule|string>|string>
     */
    public function rules(): array
    {
        return [
            'order_status_uuid' => 'required|uuid|exists:order_statuses,uuid',
            'payment_uuid' => 'required|uuid|exists:payments,uuid',
            'products' => [
                'required',
                new JsonRule(
                    $this->getProductsRule()
                ),
            ],
            'address' => [
                'required',
                new JsonRule(
                    $this->getAddressRule()
                ),
            ],
            'amount' => 'required|numeric',
        ];
    }

    /**
     * @return string[]
     */
    public function getProductsRule(): array
    {
        return [
            'product' => 'exists:products,uuid|string|required',
            'quantity' => 'required|integer|min:1',
        ];
    }

    /**
     * @return string[]
     */
    public function getAddressRule(): array
    {
        return [
            'billing' => 'required|string',
            'shipping' => 'required|string',
        ];
    }
}
