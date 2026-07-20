<?php

declare(strict_types=1);

namespace Copie\Shared\Domain;

use Copie\Shared\Domain\ValueObjects\DomainId;
use DateTimeImmutable;

/** @phpstan-consistent-constructor */
abstract class BaseEntity
{
    private DomainId $domainId;

    private DateTimeImmutable $createdAt;

    private ?DateTimeImmutable $updatedAt = null;

    /**
     * Create a new entity instance with a generated identity.
     *
     * This constructor is intended for fresh entities only.
     * For reconstituting existing entities from persistent storage,
     * use fromPersistence() instead.
     */
    protected function __construct()
    {
        $this->domainId = DomainId::generate();
        $this->createdAt = new DateTimeImmutable();
    }

    /**
     * Factory: reconstitute an existing entity from persistent storage.
     *
     * This is the ONLY entry point for infrastructure layer to
     * reconstruct a domain entity with its pre-existing identity
     * and timestamps.
     */
    public static function fromPersistence(
        DomainId $domainId,
        DateTimeImmutable $createdAt,
        ?DateTimeImmutable $updatedAt = null,
    ): static {
        $static = new static();
        $static->domainId = $domainId;
        $static->createdAt = $createdAt;
        $static->updatedAt = $updatedAt;

        return $static;
    }

    /**
     * Return the unique identifier for this entity.
     */
    public function id(): DomainId
    {
        return $this->domainId;
    }

    /**
     * Return the creation timestamp.
     */
    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Return the last update timestamp, or null if never updated.
     */
    public function updatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * Mark this entity as updated.
     */
    protected function updateTimestamp(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }

    /**
     * Two entities are equal if they share the same ID and class.
     */
    public function equals(self $other): bool
    {
        return $this->domainId->equals($other->id())
            && static::class === $other::class;
    }

    /**
     * Serialize entity to an array representation.
     */
    abstract public function toArray(): array;
}
