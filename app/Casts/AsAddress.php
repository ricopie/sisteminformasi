<?php

namespace App\Casts;

use App\ValueObjects\Address;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class AsAddress implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): Address
    {
        return new Address(
            $attributes['street'],
            $attributes['rt'],
            $attributes['rw'],
            $attributes['village'],
            $attributes['sub_district'],
            $attributes['city'],
            $attributes['province'],
            $attributes['postal_code']

        );
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): array
    {
        if (! $value instanceof Address) {
            throw new InvalidArgumentException('The given value is not an Address instance.');
        }

        return [
            'street' => $value->street,
            'rt' => $value->rt,
            'rw' => $value->rw,
            'village' => $value->village,
            'sub_district' => $value->sub_district,
            'city' => $value->city,
            'province' => $value->province,
            'postal_code' => $value->postal_code,
        ];
    }
}
