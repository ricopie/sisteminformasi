<?php

declare(strict_types=1);

namespace Copie\Shared\Domain\Attributes;

use Attribute;
use InvalidArgumentException;

#[Attribute(flags: Attribute::TARGET_PROPERTY)]
class Digits implements ValidationAttributeInterface
{
    public function __construct(
        public int $min = 1,
        public int $max = PHP_INT_MAX,
        public string $message = 'Value must be numeric digits only',
    ) {}

    public function validate(mixed $value): void
    {
        if ($value === null) {
            return;
        }

        if (! is_string($value) || ! ctype_digit($value)) {
            throw new InvalidArgumentException($this->message);
        }

        $length = strlen($value);

        if ($length < $this->min || $length > $this->max) {
            throw new InvalidArgumentException(
                sprintf('Value must be between %d and %d digits.', $this->min, $this->max)
            );
        }
    }
}
