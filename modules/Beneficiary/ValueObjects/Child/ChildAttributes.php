<?php

namespace Modules\Beneficiary\ValueObjects\Child;

use Illuminate\Contracts\Support\Arrayable;

final class ChildAttributes implements Arrayable
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private Guardian $guardian,
        private Education $education,
        private array $hobbies = [],
        private ?string $entry_date = null,
    ) {}

    public function toArray(): array
    {
        return array_map(
            fn ($value) => $value instanceof Arrayable ? $value->toArray() : $value,
            get_object_vars($this)
        );
    }
}
