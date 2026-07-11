<?php

declare(strict_types=1);

namespace Shared\Entities;

use DateTimeImmutable;
use Illuminate\Contracts\Support\Arrayable;
use Shared\Exceptions\InvalidIdentifierException;
use Shared\ValueObjects\DomainId;

/** @phpstan-consistent-constructor */
abstract class BaseEntity implements Arrayable
{
    private DomainId $id;

    private DateTimeImmutable $createdAt;

    private ?DateTimeImmutable $updatedAt = null;

    /** @var array<object> */
    private array $domainEvents = [];

    /**
     * Create a new entity instance with a generated identity.
     *
     * This constructor is intended for fresh entities only.
     * For reconstituting existing entities from persistent storage,
     * use fromPersistence() instead.
     */
    protected function __construct()
    {
        $this->id = static::generateId();
        $this->createdAt = new DateTimeImmutable;
    }

    /**
     * Determine whether the given value is a valid domain id.
     *
     * Each entity may adopt its own id format (ULID, UUID, integer, etc.).
     *
     * Example: return \Illuminate\Support\Str::isUlid($value);
     */
    abstract protected static function isValidId(string $value): bool;

    /**
     * Generate a new domain id.
     *
     * Implementations should delegate to createId() so that
     * the generated value is automatically validated via isValidId().
     *
     * Example: return self::createId((string) Str::ulid());
     */
    abstract protected static function generateId(): DomainId;

    /**
     * Generate a new domain id (public entry point).
     *
     * Delegates to the entity's generateId() implementation so that
     * the generated id is always validated via isValidId().
     * Intended for infrastructure layer (Eloquent model) to generate
     * ids consistent with the domain entity.
     *
     * Example: \Domain\Beneficiaries\Entities\Beneficiary::newId()
     */
    public static function newId(): DomainId
    {
        return static::generateId();
    }

    /**
     * Factory: reconstitute an existing entity from persistent storage.
     *
     * This is the ONLY entry point for infrastructure layer to
     * reconstruct a domain entity with its pre-existing identity
     * and timestamps. Unlike the constructor (which always generates
     * a fresh id and createdAt), this method restores the exact
     * values persisted in the database.
     *
     * Usage (in repository):
     *   Beneficiary::fromPersistence(
     *       new DomainId($row->id),
     *       new \DateTimeImmutable($row->created_at),
     *       $row->updated_at ? new \DateTimeImmutable($row->updated_at) : null,
     *   );
     *
     * @param  DomainId  $id  Pre-existing entity identity
     * @param  DateTimeImmutable  $createdAt  Original creation timestamp
     * @param  DateTimeImmutable|null  $updatedAt  Optional last update timestamp
     * @return static Reconstituted entity instance
     */
    public static function fromPersistence(
        DomainId $id,
        DateTimeImmutable $createdAt,
        ?DateTimeImmutable $updatedAt = null,
    ): static {
        if (! static::isValidId($id->value)) {
            throw InvalidIdentifierException::for($id->value, static::class);
        }

        $entity = new static;
        $entity->id = $id;
        $entity->createdAt = $createdAt;
        $entity->updatedAt = $updatedAt;

        return $entity;
    }

    /**
     * Create a validated DomainId from a raw string value.
     *
     * Validates the string against the entity's isValidId() rule
     * before wrapping it in a DomainId value object. This is a
     * convenience helper for generateId() and fromPersistence().
     */
    protected static function createId(string $value): DomainId
    {
        if (! static::isValidId($value)) {
            throw InvalidIdentifierException::for($value, static::class);
        }

        return new DomainId($value);
    }

    /**
     * Get the entity's domain identity.
     */
    public function id(): DomainId
    {
        return $this->id;
    }

    /**
     * Get the timestamp when the entity was first created.
     */
    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Get the timestamp when the entity was last updated.
     */
    public function updatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * Mark the entity as updated by refreshing the updatedAt timestamp.
     */
    protected function updateTimestamp(): void
    {
        $this->updatedAt = new DateTimeImmutable;
    }

    /**
     * Record a domain event for later dispatch.
     *
     * Events recorded here are collected and can be pulled
     * via pullDomainEvents() after the entity is persisted.
     */
    protected function recordDomainEvent(object $event): void
    {
        $this->domainEvents[] = $event;
    }

    /**
     * Retrieve and clear all recorded domain events.
     *
     * Called by the infrastructure layer after persisting the entity
     * to dispatch the events through the application bus.
     *
     * @return object[]
     */
    public function pullDomainEvents(): array
    {
        $events = $this->domainEvents;
        $this->domainEvents = [];

        return $events;
    }

    /**
     * Equality check based on identity rather than attribute values.
     *
     * Two entities are considered equal when they share the same
     * class and domain identity, regardless of their current state.
     */
    public function equals(self $other): bool
    {
        return $this->id->equals($other->id())
            && static::class === $other::class;
    }

    /**
     * Serialize the entity to an array.
     *
     * Each concrete entity must define which properties to expose.
     * Domain events and internal state should NOT be included.
     */
    abstract public function toArray(): array;
}
