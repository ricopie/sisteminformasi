<?php

namespace Modules\Beneficiary\Data\Child;

use Modules\Beneficiary\Enums\EducationLevel;
use Modules\Beneficiary\Enums\EducationStatus;
use Spatie\LaravelData\Data;

class EducationData extends Data
{
    public function __construct(
        public EducationLevel $level,
        public EducationStatus $status,
        public string $schoolName,
        public int $grade,
        public ?string $major = null,
        public ?string $nisn = null,
    ) {}
}
