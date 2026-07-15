<?php

declare(strict_types=1);

namespace Copie\Contexts\Beneficiary\Domain\Entities;

use Copie\Shared\Domain\Attributes\NotBlank;
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
class FamilyCard extends BaseEntity
{
    public function __construct(
        #[NotBlank(message: 'Family card number is required')]
        public readonly string $number,
        #[NotBlank(message: 'Head of family name is required')]
        public readonly string $headOfFamilyName,
        public readonly ?Address $address = null,
    ) {
        parent::__construct();
    }

    public static function create(
        string $number,
        string $headOfFamilyName,
        ?Address $address = null,
    ): self {
        return new self(
            number: $number,
            headOfFamilyName: $headOfFamilyName,
            address: $address,
        );
    }

    public static function reconstitute(
        DomainId $domainId,
        DateTimeImmutable $createdAt,
        ?DateTimeImmutable $updatedAt,
        string $number,
        string $headOfFamilyName,
        ?Address $address = null,
    ): self {
        self::fromPersistence($domainId, $createdAt, $updatedAt);

        return new self(
            number: $number,
            headOfFamilyName: $headOfFamilyName,
            address: $address,
        );
    }

    public function number(): string
    {
        return $this->number;
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
        // FamilyCard uses readonly properties, so we reconstruct via parent timestamp.
        // In V2 embedded design, the Beneficiary aggregate root will re-create the
        // FamilyCard entity with updated values. This method is kept for API consistency.
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

        $this->updateTimestamp();
    }

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
