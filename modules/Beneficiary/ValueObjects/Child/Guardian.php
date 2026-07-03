<?php

namespace Modules\Beneficiary\ValueObjects\Child;

use App\Concern\RecursivelyConversToArray;
use App\ValueObjects\Person;
use Illuminate\Contracts\Support\Arrayable;
use Modules\Beneficiary\Enums\GuardianRelationship;

final class Guardian implements Arrayable
{
    use RecursivelyConversToArray;

    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly Person $person,
        public readonly GuardianRelationship $relationship,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            person: Person::fromArray($data['person']),
            relationship: GuardianRelationship::tryFrom($data['relationship'])
        );
    }
}
