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

    /** Determine whether the given value is a valid identifier */
    abstract protected static function isValid(string $value): bool;

    /** Generate a new identifier */
    abstract public static function generate(): static;

    /** Check if two identifiers are equal */
    public function equals(self $other): bool
    {
        return static::class === $other::class && $this->value === $other->value;
    }

    /** Get string representation of the identifier */
    public function __toString(): string
    {
        return $this->value;
    }
}
