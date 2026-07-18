<?php

declare(strict_types=1);

namespace Copie\Shared\Domain\Exceptions;

use InvalidArgumentException;
use ReflectionClass;

final class InvalidIdentifierException extends InvalidArgumentException
{
    public static function for(string $identifier, string $type): self
    {
        return new self(
            sprintf('Invalid %s: "%s".', (new ReflectionClass($type))->getShortName(), $identifier)
        );
    }
}
