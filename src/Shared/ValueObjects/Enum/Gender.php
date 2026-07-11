<?php

declare(strict_types=1);

namespace Shared\ValueObjects\Enum;

enum Gender: string
{
    case MALE = 'male';
    case FEMALE = 'female';
}
