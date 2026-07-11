<?php

declare(strict_types=1);

namespace Domain\Beneficiaries\ValueObjects\Enum;

enum BeneficiaryType: string
{
    case CHILD = 'child';
    case ELDERLY = 'elderly';
    case DISABLED = 'disabled';
    case GENERAL = 'general';
}
