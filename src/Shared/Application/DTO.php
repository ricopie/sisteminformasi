<?php

declare(strict_types=1);

namespace Copie\Shared\Application;

abstract class DTO
{
    abstract public static function fromArray(array $data): static;

    abstract public function toArray(): array;
}
