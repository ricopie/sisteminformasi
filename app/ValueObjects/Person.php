<?php

namespace App\ValueObjects;

use App\Concern\RecursivelyConvertsToArray;
use Illuminate\Contracts\Support\Arrayable;
use InvalidArgumentException;
use Modules\Beneficiary\Enums\EducationLevel;

final readonly class Person implements Arrayable
{
    use RecursivelyConvertsToArray;

    public function __construct(
        private string $name,
        private ?string $occupation,
        private ?EducationLevel $education,
        private ?Address $address,
        private ?Contact $contact
    ) {
        if (trim($name) === '') {
            throw new InvalidArgumentException('Person name must not be empty.');
        }
    }

    /** Create Person instance from array data */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            occupation: $data['occupation'] ?? null,
            education: isset($data['education'])
                ? EducationLevel::from($data['education'])
                : null,
            address: isset($data['address'])
                ? Address::fromArray($data['address'])
                : null,
            contact: isset($data['contact'])
                ? Contact::fromArray($data['contact'])
                : null
        );
    }
}
