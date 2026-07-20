<?php

declare(strict_types=1);

namespace Copie\Shared\Domain\Attributes;

use Attribute;
use InvalidArgumentException;

#[Attribute(flags: Attribute::TARGET_PROPERTY)]
class Phone implements ValidationAttributeInterface
{
    public function __construct(
        public string $pattern = '/^(0|\+62)\d{8,13}$/',
        public string $message = 'Invalid phone number format'
    ) {
    }

    public function validate(mixed $value): void
    {
        if ($value === null) {
            return;
        }

        if (! is_string($value) || ! preg_match($this->pattern, $value)) {
            throw new InvalidArgumentException($this->message);
        }
    }
}
