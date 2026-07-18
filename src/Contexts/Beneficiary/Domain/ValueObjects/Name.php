<?php

declare(strict_types=1);

namespace Copie\Contexts\Beneficiary\Domain\ValueObjects;

use Copie\Shared\Domain\Attributes\NotBlank;
use Copie\Shared\Domain\ValueObject;

final class Name extends ValueObject
{
    public function __construct(
        #[NotBlank]
        public readonly string $firstName,
        #[NotBlank]
        public readonly string $lastName
    ) {
        $this->validate();
    }

    public static function fromArray(array $data): static
    {
        return new self(
            firstName: $data['firstName'],
            lastName: $data['lastName']
        );
    }

    public function toArray(): array
    {
        return [
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
        ];
    }

    protected function equalize(): array
    {
        return [
            $this->firstName,
            $this->lastName,
        ];
    }

    public function fullName(): string
    {
        return sprintf('%s %s', $this->firstName, $this->lastName);
    }
}
