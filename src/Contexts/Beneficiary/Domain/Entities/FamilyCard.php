<?php

declare(strict_types=1);

namespace Copie\Contexts\Beneficiary\Domain\Entities;

use Copie\Shared\Domain\BaseEntity;
use Copie\Shared\Domain\ValueObjects\Address;
use Copie\Shared\Domain\ValueObjects\DomainId;
use DateTimeImmutable;
use InvalidArgumentException;

/**
 * FamilyCard entity — internal entity within Beneficiary Aggregate.
 *
 * Represents a Family Card (Kartu Keluarga / KK) that groups
 * family members together. Managed by Beneficiary aggregate root.
 */
final class FamilyCard extends BaseEntity
{
    private string $number;

    private string $headOfFamilyName;

    private ?Address $address = null;

    protected function __construct()
    {
        parent::__construct();
    }

    /** Create a new FamilyCard. */
    public static function create(
        string $number,
        string $headOfFamilyName,
        ?Address $address = null,
    ): self {
        $entity = new self();
        $entity->number = $number;
        $entity->headOfFamilyName = $headOfFamilyName;
        $entity->address = $address;

        return $entity;
    }

    /** Reconstitute a FamilyCard from persistent storage. */
    public static function reconstitute(
        DomainId $domainId,
        DateTimeImmutable $createdAt,
        ?DateTimeImmutable $updatedAt,
        string $number,
        string $headOfFamilyName,
        ?Address $address = null,
    ): self {
        $familyCard = self::fromPersistence($domainId, $createdAt, $updatedAt);
        $familyCard->number = $number;
        $familyCard->headOfFamilyName = $headOfFamilyName;
        $familyCard->address = $address;

        return $familyCard;
    }

    // ─── Getters ──────────────────────────────────────────────

    /** Return the card number. */
    public function number(): string
    {
        return $this->number;
    }

    /** Return the head of family name. */
    public function headOfFamilyName(): string
    {
        return $this->headOfFamilyName;
    }

    /** Return the family address, or null if not set. */
    public function address(): ?Address
    {
        return $this->address;
    }

    // ─── Business Methods ─────────────────────────────────────

    /** Update the family address. */
    public function updateAddress(Address $address): void
    {
        $this->address = $address;
        $this->updateTimestamp();
    }

    /**
     * Change the head of family name.
     *
     * @throws InvalidArgumentException If name is empty after trimming.
     */
    public function changeHeadOfFamily(string $name): void
    {
        $name = trim($name);

        if ($name === '') {
            throw new InvalidArgumentException('Head of family name must not be empty.');
        }

        $this->headOfFamilyName = $name;
        $this->updateTimestamp();
    }

    /** Serialize to array. */
    public function toArray(): array
    {
        return [
            'id' => $this->id()->value,
            'number' => $this->number,
            'head_of_family_name' => $this->headOfFamilyName,
            'address' => $this->address?->toArray(),
        ];
    }
}
