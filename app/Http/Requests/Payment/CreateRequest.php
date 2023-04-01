<?php

namespace App\Http\Requests\Payment;

use App\Rules\PaymentDetailsRule;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class CreateRequest extends FormRequest
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
     * @return array<string, Rule|array|string>
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
