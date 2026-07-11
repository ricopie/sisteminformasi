<?php

namespace Shared\ValueObjects\Concern;

use Illuminate\Contracts\Support\Arrayable;

trait RecursivelyConvertsToArray
{
    /**
     * Recursively convert all properties to an array.
     *
     * Handles nested Value Objects (Arrayable), arrays of Arrayable,
     * and primitive values. This ensures a complete serialization
     * regardless of nesting depth.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return array_map(
            fn ($value) => $this->transformValue($value),
            get_object_vars($this)
        );
    }

    /**
     * Transform a single value into its array representation.
     *
     * - Arrayable instances → toArray()
     * - Arrays → recursively transform each element
     * - Primitives (string, int, null, etc.) → returned as-is
     */
    private function transformValue(mixed $value): mixed
    {
        if ($value instanceof Arrayable) {
            return $value->toArray();
        }

        if (is_array($value)) {
            return array_map(
                fn ($item) => $this->transformValue($item),
                $value
            );
        }

        return $value;
    }
}
