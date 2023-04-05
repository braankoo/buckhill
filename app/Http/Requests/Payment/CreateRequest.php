<?php

namespace App\Http\Requests\Payment;

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
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<string|JsonRule>|string>
     */
    public function rules(): array
    {
        return [
            'type' => 'bail|required|in:credit_card,cash_on_delivery,bank_transfer',
            'details' => [
                'required',
                new JsonRule(
                    $this->getValidationRules()
                ),
            ],
        ];
    }

    /**
     * @return array<string, array<string>|string>
     */
    private function getValidationRules(): array
    {
        return match ($this->input('type')) {
            'credit_card' => [
                'card_number' => 'required|string',
                'expiration_date' => 'required|string',
                'cvv' => 'required|string',
            ],
            'cash_on_delivery' => [
                'first_name' => 'string',
                'last_name' => 'string',
                'address' => 'required|string',
            ],
            'bank_transfer' => [
                'account_number' => 'required|string',
                'routing_number' => 'required|string',
            ],
            default => [],
        };
    }
}
