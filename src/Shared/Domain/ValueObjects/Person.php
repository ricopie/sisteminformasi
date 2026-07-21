<?php

declare(strict_types=1);

namespace Copie\Shared\Domain\ValueObjects;

use Copie\Shared\Domain\Attributes\NotBlank;
use Copie\Shared\Domain\ValueObject;

final class Person extends ValueObject
{
    final public function __construct(
        #[NotBlank(message: 'Person name is required')]
        public readonly string $name,
        public readonly ?string $occupation = null,
        public readonly ?string $education = null,
        public readonly ?Address $address = null,
        public readonly ?Contact $contact = null,
    ) {
        $this->validate();
    }

    public static function fromArray(array $data): static
    {
        return new self(
            name: $data['name'] ?? '',
            occupation: $data['occupation'] ?? null,
            education: $data['education'] ?? null,
            address: isset($data['address'])
                ? Address::fromArray($data['address'])
                : null,
            contact: isset($data['contact'])
                ? Contact::fromArray($data['contact'])
                : null,
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'occupation' => $this->occupation,
            'education' => $this->education,
            'address' => $this->address?->toArray(),
            'contact' => $this->contact?->toArray(),
        ];
    }

    protected function equalize(): array
    {
        return $this->toArray();
    }
}
