<?php

namespace Modules\Beneficiary\Enums;

enum BeneficiaryType: string
{
    case CHILD = 'child';
    case ELDERLY = 'elderly';
    case DISABLED = 'disabled';
    case GENERAL = 'general';
}
