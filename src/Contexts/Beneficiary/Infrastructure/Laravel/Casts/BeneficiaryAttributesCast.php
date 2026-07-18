<?php

declare(strict_types=1);

namespace Copie\Contexts\Beneficiary\Infrastructure\Laravel\Casts;

use Copie\Contexts\Beneficiary\Domain\Enums\BeneficiaryType;
use Copie\Contexts\Beneficiary\Domain\ValueObjects\ChildAttributes;
use Copie\Contexts\Beneficiary\Domain\ValueObjects\SpecificAttributes;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * Custom Eloquent cast for the specific_attributes column.
 *
 * Dispatches to the correct SpecificAttributes implementation
 * based on the beneficiary type.
 */
class BeneficiaryAttributesCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): ?SpecificAttributes
    {
        if ($value === null || $value === '') {
            return null;
        }

        $data = json_decode((string) $value, associative: true);

        if (! \is_array($data)) {
            return null;
        }

        return match (BeneficiaryType::tryFrom($attributes['type'] ?? '')) {
            BeneficiaryType::CHILD => ChildAttributes::fromArray($data),
            default => null,
        };
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if ($value instanceof SpecificAttributes) {
            return json_encode($value->toArray());
        }

        if (\is_array($value)) {
            return json_encode($value);
        }

        return null;
    }
}
