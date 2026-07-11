<?php

namespace Domain\Beneficiaries\Entities;

use DateTimeImmutable;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Shared\Entities\BaseEntity;
use Shared\ValueObjects\Address;
use Shared\ValueObjects\DomainId;

/**
 * Represents a Family Card (Kartu Keluarga / KK) aggregate root.
 *
 * A FamilyCard serves as the head of a household unit and can be
 * associated with multiple Beneficiaries.
 */
final class FamilyCard extends BaseEntity
{
    private string $familyCardNumber;

    private string $headOfFamilyName;

    private ?Address $address = null;

    protected function __construct()
    {
        parent::__construct();
    }

    protected static function isValidId(string $value): bool
    {
        return Str::isUlid($value);
    }

    protected static function generateId(): DomainId
    {
        return self::createId((string) Str::ulid());
    }

    /**
     * Register a new FamilyCard.
     *
     * @param  string  $familyCardNumber  Family Card Number aka. KK ID
     * @param  string  $headOfFamilyName  Name of head of family
     * @param  Address|null  $address  Family Address
     */
    public static function register(
        string $familyCardNumber,
        string $headOfFamilyName,
        ?Address $address = null,
    ): self {
        $familyCardNumber = trim($familyCardNumber);
        $headOfFamilyName = trim($headOfFamilyName);

        if ($familyCardNumber === '') {
            throw new InvalidArgumentException('Family card number must not be empty.');
        }

        if ($headOfFamilyName === '') {
            throw new InvalidArgumentException('Head of family name must not be empty.');
        }

        $entity = new self;
        $entity->familyCardNumber = $familyCardNumber;
        $entity->headOfFamilyName = $headOfFamilyName;
        $entity->address = $address;

        return $entity;
    }

    /**
     * Reconstitute a FamilyCard from persistent storage.
     */
    public static function reconstitute(
        DomainId $id,
        DateTimeImmutable $createdAt,
        ?DateTimeImmutable $updatedAt,
        string $familyCardNumber,
        string $headOfFamilyName,
        ?Address $address = null,
    ): self {
        $entity = self::fromPersistence($id, $createdAt, $updatedAt);
        $entity->familyCardNumber = $familyCardNumber;
        $entity->headOfFamilyName = $headOfFamilyName;
        $entity->address = $address;

        return $entity;
    }

    public function familyCardNumber(): string
    {
        return $this->familyCardNumber;
    }

    public function headOfFamilyName(): string
    {
        return $this->headOfFamilyName;
    }

    public function address(): ?Address
    {
        return $this->address;
    }

    /**
     * Update the family's address.
     */
    public function updateAddress(Address $address): void
    {
        $this->address = $address;
        $this->updateTimestamp();
    }

    /**
     * Update the head of family name.
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

    public function toArray(): array
    {
        return [
            'id' => $this->id()->value,
            'family_card_number' => $this->familyCardNumber,
            'head_of_family_name' => $this->headOfFamilyName,
            'address' => $this->address?->toArray(),
        ];
    }
}
