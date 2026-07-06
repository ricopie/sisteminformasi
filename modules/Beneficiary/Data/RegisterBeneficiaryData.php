<?php

namespace Modules\Beneficiary\Data;

use Modules\Beneficiary\Data\Child\ChildAttributesData;
use Modules\Beneficiary\Enums\BeneficiaryType;
use Spatie\LaravelData\Data;

class RegisterBeneficiaryData extends Data
{
    public function __construct(
        public string $nik,
        public BeneficiaryType $type,
        public string $full_name,
        public ?string $nick_name,
        public string $birth_place,
        public string $birth_date,
        public string $gender,
        public FamilyCardData $family_card,
        public ?ChildAttributesData $attributes = null,
        /** @var GuardianData[]|null */
        public ?array $guardians = null,
    ) {}
}
