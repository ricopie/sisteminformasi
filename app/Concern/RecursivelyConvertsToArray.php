<?php

namespace App\Concern;

use Illuminate\Contracts\Support\Arrayable;

trait RecursivelyConvertsToArray
{
    public function toArray(): array
    {
        return array_map(
            fn ($value) => $value instanceof Arrayable ? $value->toArray() : $value,
            get_object_vars($this)
        );
    }
}
