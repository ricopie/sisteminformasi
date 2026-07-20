<?php

declare(strict_types=1);

namespace Copie\Shared\Domain\Attributes;

use Attribute;
use InvalidArgumentException;

#[Attribute(flags: Attribute::TARGET_PROPERTY)]
class Email implements ValidationAttributeInterface
{
    public function __construct(
        public string $message = 'Invalid email format'
    ) {
    }

    public function validate(mixed $value): void
    {
        if ($value === null) {
            return;
        }

        if (! is_string($value) || ! filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException($this->message);
        }
    }
}
