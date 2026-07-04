<?php

namespace Modules\Beneficiary\ValueObjects;

use App\ValueObjects\Identifier;
use Illuminate\Support\Str;

final readonly class GuardianId extends Identifier
{
    public static function generate(): static
    {
        return new self((string) Str::ulid());
    }

    protected static function isValid(string $value): bool
    {
        return Str::isUlid($value);
    }
}
