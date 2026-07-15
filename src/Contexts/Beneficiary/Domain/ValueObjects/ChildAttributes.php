<?php

declare(strict_types=1);

namespace Copie\Contexts\Beneficiary\Domain\ValueObjects;

use InvalidArgumentException;

class ChildAttributes implements SpecificAttributes
{
    /**
     * @param  Education  $education  Current education
     * @param  Education[]  $educationHistory  Past education records
     * @param  string[]  $hobbies  List of hobbies
     */
    public function __construct(
        public readonly Education $education,
        public readonly array $educationHistory = [],
        public readonly array $hobbies = [],
    ) {
        $this->validateEducationHistory();
        $this->validateHobbies();
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

    private function validateEducationHistory(): void
    {
        foreach ($this->educationHistory as $item) {
            if (! $item instanceof Education) {
                throw new InvalidArgumentException(
                    'Each education history item must be an Education instance.'
                );
            }
        }
    }

    private function validateHobbies(): void
    {
        foreach ($this->hobbies as $hobby) {
            if (! is_string($hobby) || trim($hobby) === '') {
                throw new InvalidArgumentException(
                    'Each hobby must be a non-empty string.'
                );
            }
        }
    }
}
