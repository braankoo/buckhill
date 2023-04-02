<?php

namespace App\Rules\Product;

use App\Models\Brand;
use App\Models\File;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class MetaDataRule implements ValidationRule
{


    protected $allowed = ['brand', 'image'];

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $values = json_decode($value, true);

        if (!is_array($values)) {
            $fail('Metadata must be valid JSON');
            return;
        }
        if (empty($values)) {
            $fail('Metadata JSON cant be empty');
            return;
        }


        if (array_diff(array_keys($values), $this->allowed)) {
            $fail('Metadata must contain brand and image');
            return;
        }
        if (!Brand::where('uuid', $values['brand'])->exists()) {
            $fail('Brand with given UUID does not exist.');
        }
        if (!File::where('uuid', $values['image'])->exists()) {
            $fail('File with given UUID does not exist.');
        }
    }

    public function message(): string
    {
        return 'The :attribute field must be a valid JSON string.';
    }
}
