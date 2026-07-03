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
        private Person $person,
        private GuardianRelationship $relationship,
    ) {}
}
