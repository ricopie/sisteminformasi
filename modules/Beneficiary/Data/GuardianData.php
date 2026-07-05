<?php

namespace Modules\Beneficiary\Data;

use Modules\Beneficiary\Enums\GuardianRelationship;
use Spatie\LaravelData\Data;

class GuardianData extends Data
{
    public function __construct(
        /** @var array{name: string, occupation?: string|null, education?: string|null, address?: array|null, contact?: array|null} */
        public array $person,
        public GuardianRelationship $relationship,
    ) {}
}
