<?php

namespace Modules\Beneficiary\ValueObjects\Child;

use App\Concern\RecursivelyConvertsToArray;
use Illuminate\Contracts\Support\Arrayable;
use Modules\Beneficiary\Enums\EducationLevel;
use Modules\Beneficiary\Enums\EducationStatus;

final readonly class Education implements Arrayable
{
    use RecursivelyConvertsToArray;

    public function __construct(
        public readonly EducationLevel $level,
        public readonly EducationStatus $status,
        public readonly string $schoolName,
        public readonly int $grade,
        public readonly ?string $major = null,
        public readonly ?string $nisn = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            level: EducationLevel::tryFrom($data['level'] ?? ''),
            status: EducationStatus::tryFrom($data['status'] ?? ''),
            schoolName: $data['schoolName'],
            grade: $data['grade'],
            major: $data['major'] ?? null,
            nisn: $data['nisn'] ?? null,
        );
    }
}
