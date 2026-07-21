<?php

declare(strict_types=1);

namespace Copie\Shared\Application;

use Copie\Shared\Domain\Validator\AttributeValidator;
use Copie\Shared\Domain\Validator\ValidationException;

abstract class DTO
{
    private static ?AttributeValidator $attributeValidator = null;

    abstract public static function fromArray(array $data): static;

    abstract public function toArray(): array;

    /**
     * Validate this DTO against its validation attributes.
     *
     * @throws ValidationException
     */
    protected function validate(): void
    {
        $this->getValidator()->validate($this);
    }

    private function getValidator(): AttributeValidator
    {
        if (! self::$attributeValidator instanceof AttributeValidator) {
            self::$attributeValidator = new AttributeValidator();
        }

        return self::$attributeValidator;
    }
}
