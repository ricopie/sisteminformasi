<?php

declare(strict_types=1);

namespace Domain\Beneficiaries\ValueObjects;

use Illuminate\Contracts\Support\Arrayable;

interface SpecificAttributes extends Arrayable
{
    /**
     * Serialize the attributes to an array for persistence.
     *
     * The returned array must be encodable to JSON (strings, numbers, arrays).
     * Nested Value Objects should implement Arrayable so they can be
     * recursively converted via toArray().
     *
     * @return array<string, mixed>
     */
    public function toArray(): array;

    /**
     * Reconstitute attributes from a previously persisted array.
     *
     * This is the inverse of toArray() — it restores the full Value Object
     * graph from the flat array data that was stored in the database.
     *
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): static;
}
