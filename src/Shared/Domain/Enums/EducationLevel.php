<?php

declare(strict_types=1);

namespace Copie\Shared\Domain\Enums;

enum EducationLevel: string
{
    case NONE = 'none';
    case ELEMENTARY = 'elementary';
    case JUNIOR_HIGH = 'junior_high';
    case SENIOR_HIGH = 'senior_high';
    case DIPLOMA = 'diploma';
    case BACHELOR = 'bachelor';
    case MASTER = 'master';
    case DOCTOR = 'doctor';
}
