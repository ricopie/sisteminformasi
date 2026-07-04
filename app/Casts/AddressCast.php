<?php

namespace App\Casts;

use App\ValueObjects\Address;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class AddressCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if ($value === null || $value === '') {
            return null;
        }

        $data = json_decode($value, true);

        if (! \is_array($data)) {
            return null;
        }

        return Address::fromArray($data);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if ($value === null) {
            return null;
        }

        if (\is_array($value)) {
            $value = Address::fromArray($value);
        }

        if (! $value instanceof Address) {
            throw new InvalidArgumentException('The given value is not an Address instance or valid array.');
        }

        $encoded = json_encode($value->toArray());

        if ($encoded === false) {
            throw new InvalidArgumentException('Failed to serialize address.');
        }

        return $encoded;
    }
}
