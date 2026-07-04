<?php

namespace App\ValueObjects;

use App\Exceptions\InvalidIdentifierException;

abstract readonly class Identifier
{
    public function __construct(public string $value)
    {
        if (! static::isValid($value)) {
            throw InvalidIdentifierException::for(
                $value,
                static::class,
            );
        }
    }

    /**
     * Determine whether the given identifier is valid.
     *
     * Implementations typically validate using
     * Illuminate\Support\Str::isUlid() or Str::isUuid().
     */
    abstract protected static function isValid(string $value): bool;

    /**
     * Generate a new identifier.
     *
     * Implementations may use ULID or UUID depending on the concrete type.
     */
    abstract public static function generate(): static;

    public function equals(self $other): bool
    {
        return static::class === $other::class && $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
