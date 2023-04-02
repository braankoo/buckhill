<?php

namespace App\Http\Requests\Product;

use App\Rules\Product\MetaDataRule;
use Illuminate\Contracts\Validation\Rule;
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
     * @return array<string, Rule|array|string>
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
