<?php

declare(strict_types=1);

namespace Copie\Shared\Domain\Attributes;

use Attribute;
use Copie\Shared\Domain\Validator\AttributeValidator;

#[Attribute(flags: Attribute::TARGET_PROPERTY)]
class Valid implements ValidationAttributeInterface
{
    public function validate(mixed $value): void
    {
        if ($value === null || ! is_object($value)) {
            return;
        }

        $attributeValidator = new AttributeValidator();
        $attributeValidator->validate($value);
    }
}
