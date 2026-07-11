<?php

declare(strict_types=1);

namespace Domain\Beneficiaries\ValueObjects\Enum;

use InvalidArgumentException;

readonly class NationalIdentityNumber
{
    public function __construct(public string $value)
    {
        if (! preg_match('/^\d{16}$/', $value)) {
            throw new InvalidArgumentException('NIK must be 16 digits.');
        }
    }
}
