<?php

declare(strict_types=1);

namespace Copie\Contexts\Beneficiary\Domain\Exceptions;

use Copie\Contexts\Beneficiary\Domain\Enums\BeneficiaryType;
use DomainException;

/**
 * Exception thrown when beneficiary attributes don't match the expected type.
 */
final class BeneficiaryAttributeException extends DomainException
{
    public static function missingAttributes(BeneficiaryType $beneficiaryType): static
    {
        return new self(match ($beneficiaryType) {
            BeneficiaryType::CHILD => 'Child beneficiaries must have child attribute data.',
            BeneficiaryType::ELDERLY => 'Elderly beneficiaries must have elderly attribute data.',
            BeneficiaryType::DISABLED => 'Disability beneficiaries must have disability attribute data.',
        });
    }

    public static function attributesNotAllowed(BeneficiaryType $beneficiaryType): static
    {
        return new self(match ($beneficiaryType) {
            BeneficiaryType::CHILD => 'Custom attributes are not allowed for child types.',
            BeneficiaryType::ELDERLY => 'Custom attributes are not allowed for elderly types.',
            BeneficiaryType::DISABLED => 'Custom attributes are not allowed for disability types.',
        });
    }
}
