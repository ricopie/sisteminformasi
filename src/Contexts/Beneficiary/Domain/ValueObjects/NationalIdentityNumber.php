<?php

declare(strict_types=1);

namespace Copie\Contexts\Beneficiary\Domain\ValueObjects;

use Copie\Shared\Domain\Attributes\Digits;
use Copie\Shared\Domain\Attributes\NotBlank;
use Copie\Shared\Domain\ValueObject;

class NationalIdentityNumber extends ValueObject
{
    public function __construct(
        #[NotBlank(message: 'NIK is required')]
        #[Digits(min: 16, max: 16, message: 'NIK must be exactly 16 digits')]
        public readonly string $value,
    ) {
        $this->validate();
    }

    public static function fromArray(array $data): static
    {
        return new self(
            value: $data['value'],
        );
    }

    public function toArray(): array
    {
        return [
            'value' => $this->value,
        ];
    }

    protected function equalize(): array
    {
        return [
            $this->value,
        ];
    }
}
