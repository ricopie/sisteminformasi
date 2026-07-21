<?php

declare(strict_types=1);

namespace Copie\Contexts\Beneficiary\Domain\Entities;

use Copie\Shared\Domain\ValueObjects\Address;
use InvalidArgumentException;

/**
 * FamilyCard value object — embedded within Beneficiary Aggregate.
 *
 * Represents a Family Card (Kartu Keluarga / KK).
 * Stored denormalized within the beneficiaries table.
 * Immutable — replaced entirely when data changes.
 */
final readonly class FamilyCard
{
    public function __construct(
        private string $number,
        private string $headOfFamilyName,
        private ?Address $address = null,
    ) {
        if (trim($headOfFamilyName) === '') {
            throw new InvalidArgumentException('Head of family name must not be empty.');
        }
    }

    /** Create a new FamilyCard. */
    public static function create(
        string $number,
        string $headOfFamilyName,
        ?Address $address = null,
    ): self {
        return new self($number, $headOfFamilyName, $address);
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

    // ─── Equality ─────────────────────────────────────────────

    /** Two FamilyCards are equal if all properties match. */
    public function equals(self $other): bool
    {
        if ($this->number !== $other->number
            || $this->headOfFamilyName !== $other->headOfFamilyName) {
            return false;
        }

        return $this->compareAddresses($this->address, $other->address);
    }

    /** Compare two addresses, handling null values. */
    private function compareAddresses(?Address $address1, ?Address $address2): bool
    {
        if ($address1 === null && $address2 === null) {
            return true;
        }

        if ($address1 === null || $address2 === null) {
            return false;
        }

        return $address1->equals($address2);
    }

    /** Serialize to array (without id — it's a value object). */
    public function toArray(): array
    {
        return [
            'number' => $this->number,
            'head_of_family_name' => $this->headOfFamilyName,
            'address' => $this->address?->toArray(),
        ];
    }
}
