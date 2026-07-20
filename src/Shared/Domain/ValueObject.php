<?php

declare(strict_types=1);

namespace Copie\Shared\Domain;

use Copie\Shared\Domain\Validator\AttributeValidator;
use Copie\Shared\Domain\Validator\ValidationException;

abstract class ValueObject
{
    private static ?AttributeValidator $attributeValidator = null;

    /**
     * Reconstitute value object from a persisted array.
     */
    abstract public static function fromArray(array $data): static;

    /**
     * Serialize value object to an array.
     */
    abstract public function toArray(): array;

    /**
     * Return normalized array representation for equality comparison.
     */
    abstract protected function equalize(): array;

    /**
     * Two value objects are equal if their normalized representations are identical.
     */
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
