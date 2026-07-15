<?php

declare(strict_types=1);

namespace Copie\Contexts\Beneficiary\Domain\Enums;

enum GuardianRelationship: string
{
    case FATHER = 'father';
    case MOTHER = 'mother';
    case GRANDFATHER = 'grandfather';
    case GRANDMOTHER = 'grandmother';
    case UNCLE = 'uncle';
    case AUNT = 'aunt';
    case SIBLING = 'sibling';
    case FOSTER_PARENT = 'foster_parent';
    case LEGAL_GUARDIAN = 'legal_guardian';
    case OTHER = 'other';
}
