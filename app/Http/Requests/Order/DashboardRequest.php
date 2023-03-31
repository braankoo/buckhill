<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class DashboardRequest extends FormRequest
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
            'dateRange.from' => 'date|required_with:dateRange.to',
            'dateRange.to' => 'date|required_with:dateRange.from|after_or_equal:dateRange.from',
            'fixedRange' => 'in:today,monthly,yearly'
        ];
    }
}
