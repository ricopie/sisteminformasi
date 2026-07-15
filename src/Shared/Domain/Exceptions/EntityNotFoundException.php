<?php

declare(strict_types=1);

namespace Copie\Shared\Domain\Exceptions;

use RuntimeException;

final class EntityNotFoundException extends RuntimeException
{
    public static function for(string $id, string $entityType): static
    {
        return new self(sprintf("%s with ID '%s' not found.", $entityType, $id));
    }
}
