<?php

declare(strict_types=1);

namespace Copie\Shared\Domain\Attributes;

use Attribute;
use InvalidArgumentException;

#[Attribute(flags: Attribute::TARGET_PROPERTY)]
class NotBlank implements ValidationAttributeInterface
{
    public function __construct(
        public string $message = 'Value must not be blank'
    ) {
    }

    public function validate(mixed $value): void
    {
        if ($value === null || (is_string($value) && trim($value) === '')) {
            throw new InvalidArgumentException($this->message);
        }
    }
}
