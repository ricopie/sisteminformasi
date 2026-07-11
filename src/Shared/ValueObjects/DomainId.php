<?php

declare(strict_types=1);

namespace Shared\ValueObjects;

use Shared\Exceptions\InvalidIdentifierException;
use Stringable;

readonly class DomainId implements Stringable
{
    public function __construct(public string $value)
    {
        if (trim($value) === '') {
            throw InvalidIdentifierException::for(
                $value,
                static::class,
            );
        }
    }

    /** Equality checking based on identity */
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
