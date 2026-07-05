<?php

namespace Modules\Beneficiary\ValueObjects\Child;

use App\Concern\RecursivelyConvertsToArray;
use Illuminate\Contracts\Support\Arrayable;
use InvalidArgumentException;

final class ChildAttributes implements Arrayable
{
    use RecursivelyConvertsToArray;

    public function __construct(
        private Education $education,
        private array $educationHistory = [],
        private array $hobbies = [],
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
    public static function fromArray(array $data): self
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

    public function education(): Education
    {
        return $this->education;
    }

    public function educationHistory(): array
    {
        return $this->educationHistory;
    }

    public function hobbies(): array
    {
        return $this->hobbies;
    }
}
