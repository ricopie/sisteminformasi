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
        public readonly EducationLevel $level,
        public readonly string $schoolName,
        public readonly int $grade,
        public readonly ?string $major = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            level: EducationLevel::tryFrom($data['level']),
            schoolName: $data['schoolName'],
            grade: $data['grade'],
            major: $data['major'] ?? null
        );
    }
}
