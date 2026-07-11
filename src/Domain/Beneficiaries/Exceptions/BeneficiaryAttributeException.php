<?php

namespace Domain\Beneficiaries\Exceptions;

use Domain\Beneficiaries\ValueObjects\Enum\BeneficiaryType;
use DomainException;

final class BeneficiaryAttributeException extends DomainException
{
    public static function missingAttributes(BeneficiaryType $type): self
    {
        return new self(
            match ($type) {
                BeneficiaryType::CHILD => 'Child beneficiaries must have child attribute data.',
                BeneficiaryType::ELDERLY => 'Elderly beneficiaries must have elderly attribute data.',
                BeneficiaryType::DISABLED => 'Disability beneficiaries must have disability attribute data.',
                BeneficiaryType::GENERAL => 'General beneficiaries must have general attribute data.',
            }
        );
    }

    public static function attributesNotAllowed(BeneficiaryType $type): self
    {
        return new self(
            match ($type) {
                BeneficiaryType::CHILD => 'Custom attributes are not allowed for child types.',
                BeneficiaryType::ELDERLY => 'Custom attributes are not allowed for elderly types.',
                BeneficiaryType::DISABLED => 'Custom attributes are not allowed for disability types.',
                BeneficiaryType::GENERAL => 'Custom attributes are not allowed for general types.',
            }
        );
    }
}
