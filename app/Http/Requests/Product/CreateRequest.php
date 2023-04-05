<?php

namespace App\Http\Requests\Product;

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
     * @return array<string, array<string|JsonRule>|string>
     */
    public function rules(): array
    {
        return [
            'category_uuid' => 'bail|required|exists:categories,uuid',
            'title' => 'required|string|unique:products,title',
            'price' => 'required|numeric',
            'description' => 'required',
            'metadata' => [
                'required',
                new JsonRule(
                    $this->getMetaDataRules()
                ),
            ],
        ];
    }

    /**
     * @return string[]
     */
    public function getMetaDataRules(): array
    {
        return [
            'brand' => 'required|exists:brands,uuid',
            'image' => 'required|exists:files,uuid',
        ];
    }
}
