<?php

declare(strict_types=1);

namespace Copie\Shared\Domain;

use Copie\Shared\Domain\Validator\AttributeValidator;
use Copie\Shared\Domain\Validator\ValidationException;

abstract class ValueObject
{
    private static ?AttributeValidator $attributeValidator = null;

    abstract public static function fromArray(array $data): static;

    abstract public function toArray(): array;

    abstract protected function equalize(): array;

    public function equals(self $other): bool
    {
        return $this->equalize() === $other->equalize();
    }

    /**
     * Validate this object against its attributes.
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
            self::$attributeValidator = new AttributeValidator;
        }

        return self::$attributeValidator;
    }
}
