<?php

namespace Modules\Beneficiary\ValueObjects\Child;

use App\Concern\RecursivelyConvertsToArray;
use Illuminate\Contracts\Support\Arrayable;

final class ChildAttributes implements Arrayable
{
    use RecursivelyConvertsToArray;

    /**
     * Create a new class instance.
     */
    public function __construct(
        private Education $education,
        private array $educationHistory = [],
        private array $hobbies = [],
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            education: Education::fromArray($data['education']),
            educationHistory: $data['educationHistory'] ?? [],
            hobbies: $data['hobbies'] ?? []
        );
    }

    public function educationHistory(): array
    {
        return $this->educationHistory;
    }

    public function education(): Education
    {
        return $this->education;
    }

    public function hobbies(): array
    {
        return $this->hobbies;
    }
}
