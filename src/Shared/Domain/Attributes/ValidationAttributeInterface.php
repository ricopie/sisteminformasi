<?php

declare(strict_types=1);

namespace Copie\Shared\Domain\Attributes;

use InvalidArgumentException;

interface ValidationAttributeInterface
{
    /**
     * Validate the given value.
     *
     * @throws InvalidArgumentException if validation fails
     */
    public function validate(mixed $value): void;
}
