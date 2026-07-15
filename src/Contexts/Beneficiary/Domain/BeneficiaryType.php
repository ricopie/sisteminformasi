<?php

declare(strict_types=1);

namespace Copie\Contexts\Beneficiary\Domain;

enum BeneficiaryType: string
{
    case Child = 'child';
    case Elderly = 'elderly';
    case Disabled = 'disabled';
    case General = 'general';
}
