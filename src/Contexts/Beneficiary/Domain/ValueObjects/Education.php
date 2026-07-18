<?php

declare(strict_types=1);

namespace Copie\Contexts\Beneficiary\Domain\ValueObjects;

use Copie\Contexts\Beneficiary\Domain\Enums\EducationStatus;
use Copie\Shared\Domain\Attributes\NotBlank;
use Copie\Shared\Domain\Enums\EducationLevel;
use Copie\Shared\Domain\ValueObject;
use InvalidArgumentException;

final class Education extends ValueObject
{
    public function __construct(
        public readonly EducationLevel $level,
        public readonly EducationStatus $status,
        #[NotBlank(message: 'School name is required')]
        public readonly string $schoolName,
        public readonly int $grade,
        public readonly ?string $major = null,
        public readonly ?string $nisn = null,
    ) {
        $this->validate();
        $this->validateGrade();
        $this->validateNisn();
    }

    public static function fromArray(array $data): static
    {
        return new self(
            level: EducationLevel::from($data['level']),
            status: EducationStatus::from($data['status']),
            schoolName: $data['schoolName'],
            grade: (int) $data['grade'],
            major: $data['major'] ?? null,
            nisn: $data['nisn'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'level' => $this->level->value,
            'status' => $this->status->value,
            'schoolName' => $this->schoolName,
            'grade' => $this->grade,
            'major' => $this->major,
            'nisn' => $this->nisn,
        ];
    }

    protected function equalize(): array
    {
        return [
            $this->level,
            $this->status,
            $this->schoolName,
            $this->grade,
            $this->major,
            $this->nisn,
        ];
    }

    private function validateGrade(): void
    {
        if ($this->grade < 1 || $this->grade > 12) {
            throw new InvalidArgumentException('Grade must be between 1 and 12.');
        }
    }

    private function validateNisn(): void
    {
        if ($this->nisn !== null && ! preg_match('/^\d{10}$/', $this->nisn)) {
            throw new InvalidArgumentException('NISN must be exactly 10 digits.');
        }
    }
}
