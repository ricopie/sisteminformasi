<?php

declare(strict_types=1);

namespace Copie\Shared\Domain\Validator;

use Copie\Shared\Domain\Attributes\ValidationAttributeInterface;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionProperty;

class AttributeValidator
{
    /**
     * @throws ValidationException
     */
    public function validate(object $object): void
    {
        $errors = $this->collectErrors($object);

        if ($errors !== []) {
            throw new ValidationException($errors);
        }
    }

    /**
     * @return array<string, string[]>
     */
    private function collectErrors(object $object): array
    {
        $errors = [];
        $reflectionClass = new ReflectionClass($object);

        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            $propertyErrors = $this->validateProperty($object, $reflectionProperty);

            if ($propertyErrors !== []) {
                $errors[$reflectionProperty->getName()] = $propertyErrors;
            }
        }

        return $errors;
    }

    /**
     * @return string[]
     */
    private function validateProperty(object $object, ReflectionProperty $reflectionProperty): array
    {
        $errors = [];
        $value = $reflectionProperty->getValue($object);
        $attributes = $reflectionProperty->getAttributes();

        foreach ($attributes as $attribute) {
            $instance = $attribute->newInstance();

            if (! $instance instanceof ValidationAttributeInterface) {
                continue;
            }

            try {
                $instance->validate($value);
            } catch (InvalidArgumentException $e) {
                $errors[] = $e->getMessage();
            }
        }

        return $errors;
    }
}
