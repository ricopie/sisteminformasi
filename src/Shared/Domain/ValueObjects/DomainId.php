<?php

declare(strict_types=1);

namespace Copie\Shared\Domain\ValueObjects;

use Copie\Shared\Domain\Exceptions\InvalidIdentifierException;
use Stringable;
use Symfony\Component\Uid\Ulid;

readonly class DomainId implements Stringable
{
    public function __construct(public string $value)
    {
        if (! Ulid::isValid($value)) {
            throw InvalidIdentifierException::for(
                $value,
                static::class,
            );
        }
    }

    /**
     * Generate a new ULID.
     */
    public static function generate(): self
    {
        return new self((new Ulid)->toRfc4122());
    }

    public function equals(self $other): bool
    {
        return static::class === $other::class && $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
