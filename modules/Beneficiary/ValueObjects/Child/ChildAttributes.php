<?php

namespace Modules\Beneficiary\ValueObjects\Child;

use App\Concern\RecursivelyConversToArray;
use Illuminate\Contracts\Support\Arrayable;

final class ChildAttributes implements Arrayable
{
    use RecursivelyConversToArray;

    /**
     * Create a new class instance.
     */
    public function __construct(
        private Guardian $guardian,
        private Education $education,
        private array $hobbies = [],
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            guardian: Guardian::fromArray($data['guardian']),
            education: Education::fromArray($data['education']),
            hobbies: $data['hobbies'] ?? []
        );
    }

    public function guardian(): Guardian
    {
        return $this->guardian;
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
