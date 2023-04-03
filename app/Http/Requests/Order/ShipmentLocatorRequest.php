<?php

namespace App\Http\Requests\Order;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

final class ShipmentLocatorRequest extends FormRequest
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
            'orderUuid' => 'exists:orders,uuid',
            'customerUuid' => 'exists:users,uuid',
            'dateRange.from' => 'date|required_with:dateRange.to',
            'dateRange.to' => 'date|required_with:dateRange.from|after_or_equal:dateRange.from',
            'fixedRange' => 'in:today,monthly,yearly',
        ];
    }
}
