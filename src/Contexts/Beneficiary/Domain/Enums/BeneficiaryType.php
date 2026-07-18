<?php

declare(strict_types=1);

namespace Copie\Contexts\Beneficiary\Domain\Enums;

enum BeneficiaryType: string
{
    case CHILD = 'child';
    case ELDERLY = 'elderly';
    case DISABLED = 'disabled';
}
