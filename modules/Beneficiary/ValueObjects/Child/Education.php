<?php

namespace Modules\Beneficiary\ValueObjects\Child;

use App\Concern\RecursivelyConversToArray;
use Illuminate\Contracts\Support\Arrayable;
use Modules\Beneficiary\Enums\EducationLevel;

final class Education implements Arrayable
{
    use RecursivelyConversToArray;

    /**
     * Create a new class instance.
     */
    public function __construct(
        private EducationLevel $level,
        private string $school_name,
        private int $grade,
        private ?string $major = null,
    ) {}
}
