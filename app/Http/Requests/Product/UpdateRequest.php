<?php

namespace App\Http\Requests\Product;

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
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'category_uuid' => 'bail|required|exists:categories,uuid',
            'title' => 'required|string|unique:products,title',
            'price' => 'required|numeric',
            'description' => 'required',
            'metadata' => ['required', new MetaDataRule()],
        ];
    }
}
