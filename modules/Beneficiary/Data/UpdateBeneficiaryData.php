<?php

namespace Modules\Beneficiary\Data;

use Modules\Beneficiary\Data\Child\ChildAttributesData;
use Spatie\LaravelData\Data;

class UpdateBeneficiaryData extends Data
{
    public function __construct(
        public ?string $full_name = null,
        public ?string $nick_name = null,
        public ?string $birth_place = null,
        public ?string $birth_date = null,
        public ?string $gender = null,
        public ?FamilyCardData $family_card = null,
        public ?ChildAttributesData $attributes = null,
        /** @var GuardianData[]|null */
        public ?array $guardians = null,
    ) {}
}
