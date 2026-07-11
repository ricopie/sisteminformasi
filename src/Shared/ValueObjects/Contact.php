<?php

namespace Shared\ValueObjects;

use Illuminate\Contracts\Support\Arrayable;
use InvalidArgumentException;

final readonly class Contact implements Arrayable
{
    public function __construct(
        public string $phone,
        public ?string $emailAddress = null,
        public ?string $website = null,
        public ?string $addressText = null
    ) {
        $this->validatePhone($phone);

        if ($emailAddress !== null) {
            $this->validateEmail($emailAddress);
        }

        if ($website !== null) {
            $this->validateRequired('website', $website);
        }

        if ($addressText !== null) {
            $this->validateRequired('addressText', $addressText);
        }
    }

    /** Serialize contact to array */
    public function toArray(): array
    {
        return get_object_vars($this);
    }

    /** Create Contact instance from array data */
    public static function fromArray(array $data): self
    {
        return new self(...array_intersect_key($data, array_flip([
            'phone', 'emailAddress', 'website', 'addressText',
        ])));
    }

    /** Check equality with another Person */
    public function equals(self $other): bool
    {
        return $this->toArray() === $other->toArray();
    }

    /** Ensure required field is not empty */
    private function validateRequired(string $field, string $value): void
    {
        if (trim($value) === '') {
            throw new InvalidArgumentException(sprintf('Contact %s must not be empty.', $field));
        }
    }

    /** Ensure email has valid format */
    private function validatePhone(string $phone): void
    {
        if (! preg_match('/^(0|\+62)\d{8,13}$/', $phone)) {
            throw new InvalidArgumentException(sprintf("Invalid phone number format: '%s'.", $phone));
        }
    }

    private function validateEmail(string $email): void
    {
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException(sprintf("Invalid email format: '%s'.", $email));
        }
    }
}
