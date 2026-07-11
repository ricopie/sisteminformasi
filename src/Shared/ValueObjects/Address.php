<?php

namespace Shared\ValueObjects;

use Illuminate\Contracts\Support\Arrayable;
use InvalidArgumentException;
use Stringable;

final readonly class Address implements Arrayable, Stringable
{
    public function __construct(
        public string $street,
        public string $rt,
        public string $rw,
        public string $village,
        public string $district,
        public string $city,
        public string $province,
        public string $postal_code
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
        return new self(...array_intersect_key($data, array_flip([
            'street', 'rt', 'rw', 'village', 'district', 'city', 'province', 'postal_code',
        ])));
    }

    /** Check equality with another Address */
    public function equals(self $other): bool
    {
        return $this->toArray() === $other->toArray();
    }

    /** Ensure required field is not empty */
    private function validateRequired(string $field, string $value): void
    {
        if (trim($value) === '') {
            throw new InvalidArgumentException(sprintf('Address %s must not be empty.', $field));
        }
    }

    /** Ensure format is 2-3 digit numeric */
    private function validateDigit(string $field, string $value): void
    {
        if (! preg_match('/^\d{2,3}$/', $value)) {
            throw new InvalidArgumentException($field.' must be 2-3 digits.');
        }
    }

    /** Ensure postal code is exactly 5 digits */
    private function validatePostalCode(string $code): void
    {
        if (! preg_match('/^\d{5}$/', $code)) {
            throw new InvalidArgumentException(sprintf("Invalid postal code format: '%s'.", $code));
        }
    }

    public function fullAddress(): string
    {
        return \sprintf(
            '%s, RT %s/RW %s, Kel. %s, Kec. %s, %s, %s %s',
            $this->street,
            $this->rt,
            $this->rw,
            $this->village,
            $this->district,
            $this->city,
            $this->province,
            $this->postal_code
        );
    }

    public function __toString(): string
    {
        return $this->fullAddress();
    }
}
