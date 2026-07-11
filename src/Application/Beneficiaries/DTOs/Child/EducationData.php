<?php

declare(strict_types=1);

namespace Application\Beneficiaries\DTOs\Child;

use Domain\Beneficiaries\ValueObjects\Enum\EducationStatus;
use Shared\ValueObjects\Enum\EducationLevel;
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
