<?php

namespace App\Exceptions;

use InvalidArgumentException;

final class InvalidIdentifierException extends InvalidArgumentException
{
    public static function for(string $identifier, string $type): self
    {
        return new self(
            sprintf('Invalid %s: "%s".', class_basename($type), $identifier)
        );
    }
}
