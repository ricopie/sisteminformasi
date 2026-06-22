<?php

namespace App\Rules;

use App\Services\BlindIndexService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class UniqueFamilyCardNumber implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $service = app(BlindIndexService::class);
        if ($service->findFamilyCardByNumber($value)) {
            $fail('validation.unique')->translate();
        }
    }
}
