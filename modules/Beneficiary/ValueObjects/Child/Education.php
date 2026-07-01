<?php

namespace Modules\Beneficiary\ValueObjects\Child;

use Illuminate\Contracts\Support\Arrayable;

final class Education implements Arrayable
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private string $level,
        private string $school_name,
        private int $grade,
        private ?string $major = null,
    ) {}

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
