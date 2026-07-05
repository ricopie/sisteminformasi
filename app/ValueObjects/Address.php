<?php

namespace App\ValueObjects;

use Illuminate\Contracts\Support\Arrayable;
use InvalidArgumentException;

final readonly class Address implements Arrayable
{
    public function __construct(
        private string $street,
        private string $rt,
        private string $rw,
        private string $village,
        private string $district,
        private string $city,
        private string $province,
        private string $postal_code
    ) {
        $this->validateRequired('street', $street);
        $this->validateRequired('village', $village);
        $this->validateRequired('district', $district);
        $this->validateRequired('city', $city);
        $this->validateRequired('province', $province);
        $this->validateDigit('RT', $rt);
        $this->validateDigit('RW', $rw);
        $this->validatePostalCode($postal_code);
    }

    /** Serialize address to array */
    public function toArray(): array
    {
        return get_object_vars($this);
    }

    /** Create Address instance from array data */
    public static function fromArray(array $data): self
    {
        return new self(...$data);
    }

    /** Check equality with another Address */
    public function equals(Address $other): bool
    {
        return $this->toArray() === $other->toArray();
    }

    /** Ensure required field is not empty */
    private function validateRequired(string $field, string $value): void
    {
        if (trim($value) === '') {
            throw new InvalidArgumentException("Address {$field} must not be empty.");
        }
    }

    /** Ensure format is 2-3 digit numeric */
    private function validateDigit(string $field, string $value): void
    {
        if (! preg_match('/^\d{2,3}$/', $value)) {
            throw new InvalidArgumentException("{$field} must be 2-3 digits.");
        }
    }

    /** Ensure postal code is exactly 5 digits */
    private function validatePostalCode(string $code): void
    {
        if (! preg_match('/^\d{5}$/', $code)) {
            throw new InvalidArgumentException("Invalid postal code format: '{$code}'.");
        }
    }
}
