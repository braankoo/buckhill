<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\Rule;
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
     * Get the validation rules that apply to the request.
     *
     * @return array<string, Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|email|unique:users',
            'password' => ['required', new Password(8)],
            'address' => 'required',
            'phone_number' => 'required',
            'is_marketing' => 'nullable|boolean',
        ];
    }

    public function safe(?array $keys = null): \Illuminate\Support\ValidatedInput|array
    {
        $data = parent::safe($keys);
        if (is_null('is_marketing')) {
            $data['is_marketing'] = 0;
        }

        return $data;
    }
}
