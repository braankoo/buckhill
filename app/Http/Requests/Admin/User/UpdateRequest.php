<?php

namespace App\Http\Requests\Admin\User;

use App\Http\Requests\User\CreateRequest;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

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
     * Get the validation rules that apply to the request.
     *
     * @return array<string, Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'password' => ['required', 'string', new Password(8)],
            'avatar' => 'nullable',
            'address' => 'required|string',
            'phone_number' => 'required|string',
            'is_marketing' => 'string|nullable',
        ];
    }
}
