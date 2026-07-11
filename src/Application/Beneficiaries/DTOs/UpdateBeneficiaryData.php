<?php

declare(strict_types=1);

namespace Application\Beneficiaries\DTOs;

use Domain\Beneficiaries\ValueObjects\Enum\BeneficiaryType;
use Domain\Beneficiaries\ValueObjects\Enum\NationalIdentityNumber;
use Shared\ValueObjects\Enum\Gender;
use Spatie\LaravelData\Data;

class UpdateBeneficiaryData extends Data
{
    public function __construct(
        public ?NationalIdentityNumber $nik = null,
        public ?BeneficiaryType $type = null,
        public ?string $fullName = null,
        public ?string $nickName = null,
        public ?string $birthPlace = null,
        public ?string $birthDate = null,
        public ?Gender $gender = null,
        public ?FamilyCardData $familyCard = null,
        /** @var array<mixed>|null */
        public ?array $specificAttributes = null,
        /** @var GuardianData[]|null */
        public ?array $guardians = null,
    ) {}
}
