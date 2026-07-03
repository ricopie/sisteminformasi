<?php

namespace Modules\Beneficiary\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Modules\Beneficiary\Enums\BeneficiaryType;
use Modules\Beneficiary\ValueObjects\Child\ChildAttributes;

class BeneficiaryAttributesCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        $data = json_decode($value, true);

        return match (BeneficiaryType::tryFrom($attributes['type'])) {
            BeneficiaryType::CHILD => ChildAttributes::fromArray($data ?? []),
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
        if ($value instanceof Arrayable) {
            return json_encode($value->toArray());
        }

        return json_encode((array) $value);
    }
}
