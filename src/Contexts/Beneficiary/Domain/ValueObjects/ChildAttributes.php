<?php

declare(strict_types=1);

namespace Copie\Contexts\Beneficiary\Domain\ValueObjects;

final readonly class ChildAttributes implements SpecificAttributes
{
    /**
     * @param  Education  $education  Current education
     * @param  Education[]  $educationHistory  Past education records
     * @param  string[]  $hobbies  List of hobbies
     */
    public function __construct(
        public Education $education,
        public array $educationHistory = [],
        public array $hobbies = [],
    ) {
        // No need for manual type validation — PHP handles it
    }

    public static function fromArray(array $data): static
    {
        return new self(
            education: Education::fromArray($data['education']),
            educationHistory: array_map(
                Education::fromArray(...),
                $data['educationHistory'] ?? [],
            ),
            hobbies: $data['hobbies'] ?? [],
        );
    }

    public function toArray(): array
    {
        return [
            'education' => $this->education->toArray(),
            'educationHistory' => array_map(
                fn (Education $education): array => $education->toArray(),
                $this->educationHistory,
            ),
            'hobbies' => $this->hobbies,
        ];
    }
}
