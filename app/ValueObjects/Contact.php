<?php

namespace App\ValueObjects;

use Illuminate\Contracts\Support\Arrayable;

final class Contact implements Arrayable
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private string $phone,
        private ?string $emailAddress = null,
        private ?string $website = null,
        private ?string $address = null
    ) {}

    public function toArray(): array
    {
        return get_object_vars($this);
    }

    public static function fromArray(array $data): self
    {
        return new self(...$data);
    }
}
