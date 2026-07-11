<?php

namespace Domain\Beneficiaries\ValueObjects\Child;

use Domain\Beneficiaries\ValueObjects\Enum\EducationStatus;
use Illuminate\Contracts\Support\Arrayable;
use InvalidArgumentException;
use Shared\ValueObjects\Concern\RecursivelyConvertsToArray;
use Shared\ValueObjects\Enum\EducationLevel;

final readonly class Education implements Arrayable
{
    use RecursivelyConvertsToArray;

    public function __construct(
        public EducationLevel $level,
        public EducationStatus $status,
        public string $schoolName,
        public int $grade,
        public ?string $major = null,
        public ?string $nisn = null,
    ) {
        if (trim($schoolName) === '') {
            throw new InvalidArgumentException('School name must not be empty.');
        }

        if ($grade < 1 || $grade > 12) {
            throw new InvalidArgumentException('Grade must be between 1 and 12.');
        }

        if ($nisn !== null && ! preg_match('/^\d{10}$/', $nisn)) {
            throw new InvalidArgumentException('NISN must be 10 digits.');
        }
    }

    /** Create Education instance from array data */
    public static function fromArray(array $data): self
    {
        return new self(
            level: EducationLevel::tryFrom($data['level'] ?? '') ?? throw new InvalidArgumentException("Invalid education level: '".($data['level'] ?? '')."'."),
            status: EducationStatus::tryFrom($data['status'] ?? '') ?? throw new InvalidArgumentException("Invalid education status: '".($data['status'] ?? '')."'."),
            schoolName: $data['schoolName'] ?? '',
            grade: (int) ($data['grade'] ?? 0),
            major: $data['major'] ?? null,
            nisn: $data['nisn'] ?? null,
        );
    }
}
