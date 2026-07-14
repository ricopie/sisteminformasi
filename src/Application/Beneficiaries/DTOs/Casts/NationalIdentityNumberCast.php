<?php

declare(strict_types=1);

namespace Application\Beneficiaries\DTOs\Casts;

use Domain\Beneficiaries\ValueObjects\Enum\NationalIdentityNumber;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

class NationalIdentityNumberCast implements Cast
{
    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): NationalIdentityNumber
    {
        return new NationalIdentityNumber($value);
    }
}
