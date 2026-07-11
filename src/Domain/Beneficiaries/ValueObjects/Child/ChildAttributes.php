<?php

namespace Domain\Beneficiaries\ValueObjects\Child;

use Domain\Beneficiaries\ValueObjects\SpecificAttributes;
use InvalidArgumentException;
use Shared\ValueObjects\Concern\RecursivelyConvertsToArray;

final readonly class ChildAttributes implements SpecificAttributes
{
    use RecursivelyConvertsToArray;

    public function __construct(
        public Education $education,
        public array $educationHistory = [],
        public array $hobbies = [],
    ) {
        foreach ($educationHistory as $item) {
            if (! $item instanceof Education && ! is_array($item)) {
                throw new InvalidArgumentException('Each education history item must be an array or Education instance.');
            }
        }

        foreach ($hobbies as $hobby) {
            if (! is_string($hobby) || trim($hobby) === '') {
                throw new InvalidArgumentException('Each hobby must be a non-empty string.');
            }
        }
    }

    /** Create ChildAttributes instance from array data */
    public static function fromArray(array $data): static
    {
        return new self(
            education: Education::fromArray($data['education']),
            educationHistory: array_map(
                fn ($item) => is_array($item) ? Education::fromArray($item) : $item,
                $data['educationHistory'] ?? []
            ),
            hobbies: $data['hobbies'] ?? []
        );
    }
}
