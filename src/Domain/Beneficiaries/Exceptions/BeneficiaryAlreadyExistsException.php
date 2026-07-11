<?php

namespace Domain\Beneficiaries\Exceptions;

use RuntimeException;

final class BeneficiaryAlreadyExistsException extends RuntimeException
{
    public static function forNik(): self
    {
        return new self('A beneficiary with this NIK is already registered.');
    }
}
