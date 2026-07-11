<?php

namespace Shared\ValueObjects;

use Illuminate\Contracts\Support\Arrayable;
use InvalidArgumentException;
use Shared\ValueObjects\Concern\RecursivelyConvertsToArray;
use Shared\ValueObjects\Enum\EducationLevel;

/**
 * @property-read string $name
 * @property-read string|null $occupation
 * @property-read string|null $education
 * @property-read Address|null $address
 * @property-read Contact|null $contact
 */
final readonly class Person implements Arrayable
{
    use RecursivelyConvertsToArray;

    public function __construct(
        public string $name,
        public ?string $occupation,
        public ?EducationLevel $education,
        public ?Address $address,
        public ?Contact $contact
    ) {
        $this->validateRequired('name', $name);
        if ($occupation !== null) {
            $this->validateRequired('occupation', $occupation);
        }
    }

    /** Create Person instance from array data */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'] ?? throw new InvalidArgumentException('Person name is required.'),
            occupation: $data['occupation'] ?? null,
            education: isset($data['education'])
                ? EducationLevel::tryFrom($data['education']) ?? throw new InvalidArgumentException(sprintf("Invalid education level: '%s'.", $data['education']))
                : null,
            address: isset($data['address'])
                ? Address::fromArray($data['address'])
                : null,
            contact: isset($data['contact'])
                ? Contact::fromArray($data['contact'])
                : null
        );
    }

    /** Check equality with another Person */
    public function equals(self $other): bool
    {
        return $this->toArray() === $other->toArray();
    }

    /** Ensure required field is not empty */
    private function validateRequired(string $field, string $value): void
    {
        if (trim($value) === '') {
            throw new InvalidArgumentException(sprintf('Person %s must not be empty.', $field));
        }
    }
}
