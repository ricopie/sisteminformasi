<?php

declare(strict_types=1);

namespace Copie\Contexts\Beneficiary\Domain\ValueObjects;

/**
 * Interface for type-specific attributes.
 *
 * Each beneficiary type (Child, Elderly, Disabled) will implement
 * this interface to provide its own set of attributes.
 */
interface SpecificAttributes
{
    /**
     * Serialize the attributes to an array for persistence.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array;

    /**
     * Reconstitute attributes from a previously persisted array.
     *
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): static;
}
