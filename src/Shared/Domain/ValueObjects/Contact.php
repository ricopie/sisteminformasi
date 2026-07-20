<?php

declare(strict_types=1);

namespace Copie\Shared\Domain\ValueObjects;

use Copie\Shared\Domain\Attributes\Email;
use Copie\Shared\Domain\Attributes\NotBlank;
use Copie\Shared\Domain\Attributes\Phone;
use Copie\Shared\Domain\ValueObject;

final class Contact extends ValueObject
{
    final public function __construct(
        #[NotBlank(message: 'Phone is required')]
        #[Phone]
        public readonly string $phone,
        #[Email]
        public readonly ?string $emailAddress = null,
        public readonly ?string $website = null,
        public readonly ?string $addressText = null
    ) {
        $this->validate();
    }

    public static function fromArray(array $data): static
    {
        return new self(
            phone: $data['phone'],
            emailAddress: $data['emailAddress'] ?? null,
            website: $data['website'] ?? null,
            addressText: $data['addressText'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'phone' => $this->phone,
            'emailAddress' => $this->emailAddress,
            'website' => $this->website,
            'addressText' => $this->addressText,
        ];
    }

    protected function equalize(): array
    {
        return [
            $this->phone,
            $this->emailAddress,
            $this->website,
            $this->addressText,
        ];
    }
}
