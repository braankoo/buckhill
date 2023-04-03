<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

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
     * @return array<string, array<string|Password>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'password' => ['required', 'string', new Password(8)],
            'avatar' => 'nullable',
            'email' => 'email|required|unique:users',
            'address' => 'required|string',
            'phone_number' => 'required|string',
            'is_marketing' => 'boolean|nullable',
        ];
    }

    public function safe(?array $keys = null): \Illuminate\Support\ValidatedInput|array
    {
        $data = parent::safe($keys);
        if (is_null($this->input('is_marketing'))) {
            $data['is_marketing'] = 0;
        }

        return $data;
    }
}
