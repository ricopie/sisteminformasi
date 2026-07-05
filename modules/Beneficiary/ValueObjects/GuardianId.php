<?php

namespace Modules\Beneficiary\ValueObjects;

use App\ValueObjects\Identifier;
use Illuminate\Support\Str;

final readonly class GuardianId extends Identifier
{
    /** Generate a new ULID for guardian */
    public static function generate(): static
    {
        return new self((string) Str::ulid());
    }

    /** Validate that the value is a valid ULID */
    protected static function isValid(string $value): bool
    {
        return Str::isUlid($value);
    }
}
