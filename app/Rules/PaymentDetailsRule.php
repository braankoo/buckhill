<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Validator;

final class PaymentDetailsRule implements Rule
{
    private string|null $type;

    public function __construct(string|null $type)
    {
        $this->type = $type;
    }

    /**
     * @param $attribute
     * @param string $value
     *
     * @return bool
     */
    public function passes($attribute, mixed $value): bool
    {
        if (is_null($this->type)) {
            return false;
        }

        $rules = $this->validationRules()[$this->type];
        $values = json_decode($value, true);
        if (is_null($values)) {
            return false;
        }

        $validator = Validator::make(json_decode($value, true), $rules);

        return !$validator->fails();
    }

    public function message(): string
    {
        return 'Invalid payment details provided.';
    }

    private function validationRules(): array
    {
        return [
            'credit_card' => [
                'card_number' => 'required|string',
                'expiration_date' => 'required|string',
                'cvv' => 'required|string',
            ],
            'cash_on_delivery' => [
                'address' => 'required|string',
            ],
            'bank_transfer' => [
                'account_number' => 'required|string',
                'routing_number' => 'required|string',
            ],
        ];
    }
}
