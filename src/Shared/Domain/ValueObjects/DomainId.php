<?php

declare(strict_types=1);

namespace Copie\Shared\Domain\ValueObjects;

use Copie\Shared\Domain\Exceptions\InvalidIdentifierException;
use Stringable;
use Symfony\Component\Uid\Ulid;

final readonly class DomainId implements Stringable
{
    public function __construct(public string $value)
    {
        if (! Ulid::isValid($value)) {
            throw InvalidIdentifierException::for(
                $value,
                self::class,
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

    /**
     * Reconstitute a DomainId from a persisted string value.
     */
    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
