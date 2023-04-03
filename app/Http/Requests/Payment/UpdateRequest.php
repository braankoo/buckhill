<?php

namespace App\Http\Requests\Payment;

use App\Rules\PaymentDetailsRule;
use App\Rules\Product\MetaDataRule;
use Illuminate\Foundation\Http\FormRequest;

final class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<string|PaymentDetailsRule>|string>
     */
    public function rules(): array
    {
        //bail not working ?
        return [
            'type' => 'bail|required|in:credit_card,cash_on_delivery,bank_transfer',
            'details' => ['required', new PaymentDetailsRule($this->input('type') ?? null)],

        ];
    }
}
