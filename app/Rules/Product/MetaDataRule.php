<?php

namespace App\Rules\Product;

use App\Models\Brand;
use App\Models\File;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

final class MetaDataRule implements ValidationRule
{
    /**
     * @var array{string, string}
     */
    protected array $allowed = ['brand', 'image'];

    public function validate(
        string $attribute,
        mixed $value,
        Closure $fail
    ): void {
        $values = json_decode($value, true);

        if ( ! is_array($values)) {
            $fail($attribute . ' must be valid JSON');

            return;
        }

        $missingKeys = array_diff($this->allowed, array_keys($values));
        if (count($missingKeys) > 0) {
            $fail($attribute . ' must contain ' . implode(' and ', $missingKeys));

            return;
        }

        if ( ! Brand::where('uuid', $values['brand'])->exists()) {
            $fail('Brand with given UUID does not exist.');

            return;
        }

        if ( ! File::where('uuid', $values['image'])->exists()) {
            $fail('File with given UUID does not exist.');
        }
    }

    public function message(): string
    {
        return 'The :attribute field must be a valid JSON string.';
    }
}
