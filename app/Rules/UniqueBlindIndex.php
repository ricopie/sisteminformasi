<?php

namespace App\Rules;

use App\Services\BlindIndexService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class UniqueBlindIndex implements ValidationRule
{
    public function __construct(
        private string $modelClass,
        private string $hashColumn
    ) {}

    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $service = app(BlindIndexService::class);
        $hash = $service->hash($value);
        if ($this->modelClass::where($this->hashColumn, $hash)->exists()) {
            $fail('validation.unique')->translate();
        }
    }
}
