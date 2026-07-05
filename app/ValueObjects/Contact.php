<?php

namespace App\ValueObjects;

use Illuminate\Contracts\Support\Arrayable;
use InvalidArgumentException;

final readonly class Contact implements Arrayable
{
    public function __construct(
        private string $phone,
        private ?string $emailAddress = null,
        private ?string $website = null,
        private ?string $address = null
    ) {
        $this->validatePhone($phone);

        if ($emailAddress !== null) {
            $this->validateEmail($emailAddress);
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
        return new self(...$data);
    }

    /** Ensure email has valid format */
    private function validatePhone(string $phone): void
    {
        if (! preg_match('/^(0|\+62)\d{8,13}$/', $phone)) {
            throw new InvalidArgumentException("Invalid phone number format: '{$phone}'.");
        }
    }

    private function validateEmail(string $email): void
    {
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid email format: '{$email}'.");
        }
    }
}
