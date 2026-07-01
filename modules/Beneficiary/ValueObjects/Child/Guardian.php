<?php

namespace Modules\Beneficiary\ValueObjects\Child;

use App\ValueObjects\Contact;
use Illuminate\Contracts\Support\Arrayable;

final class Guardian implements Arrayable
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private string $name,
        private string $relationship,
        private ?Contact $contact = null,
    ) {}

    public function toArray(): array
    {
        return array_map(
            fn ($value) => $value instanceof Arrayable ? $value->toArray() : $value,
            get_object_vars($this)
        );
    }
}
