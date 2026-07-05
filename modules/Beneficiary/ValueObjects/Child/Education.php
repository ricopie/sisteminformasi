<?php

namespace Modules\Beneficiary\ValueObjects\Child;

use App\Concern\RecursivelyConvertsToArray;
use Illuminate\Contracts\Support\Arrayable;
use InvalidArgumentException;
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
            level: EducationLevel::from($data['level'] ?? ''),
            status: EducationStatus::from($data['status'] ?? ''),
            schoolName: $data['schoolName'],
            grade: (int) ($data['grade'] ?? 0),
            major: $data['major'] ?? null,
            nisn: $data['nisn'] ?? null,
        );
    }
}
