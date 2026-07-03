<?php

namespace App\ValueObjects;

use Illuminate\Contracts\Support\Arrayable;

final class Address implements Arrayable
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private string $street,
        private string $rt,
        private string $rw,
        private string $village,
        private string $district,
        private string $city,
        private string $province,
        private string $postal_code
    ) {}

    public function toArray(): array
    {
        return get_object_vars($this);
    }

    public static function fromArray(array $data): self
    {
        return new self(...$data);
    }

    public function equals(Address $other): bool
    {
        return $this->toArray() === $other->toArray();
    }
}
