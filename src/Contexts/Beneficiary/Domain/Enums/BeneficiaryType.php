<?php

declare(strict_types=1);

namespace Copie\Contexts\Beneficiary\Domain\Enums;

use Copie\Contexts\Beneficiary\Domain\ValueObjects\ChildAttributes;
use Copie\Contexts\Beneficiary\Domain\ValueObjects\SpecificAttributes;

enum BeneficiaryType: string
{
    case CHILD = 'child';
    case ELDERLY = 'elderly';
    case DISABLED = 'disabled';

    /**
     * Returns the specific attributes class for this type, or null if none required.
     *
     * @return class-string<SpecificAttributes>|null
     */
    public function attributeClass(): ?string
    {
        return match ($this) {
            self::CHILD => ChildAttributes::class,
            default => null,
        };
    }

    /**
     * Create SpecificAttributes from raw data array.
     *
     * @param  array<string, mixed>|null  $data
     */
    public function createAttributesFrom(?array $data): ?SpecificAttributes
    {
        if ($data === null) {
            return null;
        }

        $class = $this->attributeClass();

        return $class !== null
            ? $class::fromArray($data)
            : null;
    }
}
