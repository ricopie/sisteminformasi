<?php

namespace App\Casts;

use App\ValueObjects\Address;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class AddressCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if ($value === null || $value === '') {
            return null;
        }

        $data = json_decode($value, true);

        if (! \is_array($data)) {
            return null;
        }

        return new Address(...$data);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if ($value === null) {
            return null;
        }

        if (\is_array($value)) {
            $value = new Address(...$value);
        }

        if (! $value instanceof Address) {
            throw new InvalidArgumentException('The given value is not an Address instance or valid array.');
        }

        return json_encode($value->toArray());
    }
}
