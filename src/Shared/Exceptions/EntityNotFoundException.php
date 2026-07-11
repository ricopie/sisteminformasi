<?php

namespace Shared\Exceptions;

use RuntimeException;

final class EntityNotFoundException extends RuntimeException
{
    public static function forId(string $id, string $entityType): self
    {
        return new self(sprintf("%s with ID '%s' not found.", $entityType, $id));
    }
}
