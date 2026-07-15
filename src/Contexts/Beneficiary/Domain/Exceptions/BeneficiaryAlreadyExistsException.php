<?php

declare(strict_types=1);

namespace Copie\Contexts\Beneficiary\Domain\Exceptions;

use RuntimeException;

/**
 * Exception thrown when attempting to create a beneficiary with a NIK that already exists.
 */
final class BeneficiaryAlreadyExistsException extends RuntimeException
{
    public static function forNik(string $nik): static
    {
        return new self(sprintf('A beneficiary with NIK "%s" is already registered.', $nik));
    }
}
