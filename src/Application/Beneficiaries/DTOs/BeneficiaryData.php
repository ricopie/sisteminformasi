<?php

declare(strict_types=1);

namespace Application\Beneficiaries\DTOs;

use Domain\Beneficiaries\ValueObjects\Enum\BeneficiaryType;
use Domain\Beneficiaries\ValueObjects\Enum\NationalIdentityNumber;
use Shared\ValueObjects\Enum\Gender;
use Spatie\LaravelData\Data;

class BeneficiaryData extends Data
{
    public function __construct(
        public NationalIdentityNumber $nik,
        public BeneficiaryType $type,
        public string $fullName,
        public ?string $nickName,
        public string $birthPlace,
        public string $birthDate,
        public Gender $gender,
        public ?FamilyCardData $familyCard = null,
        /** @var array<mixed>|null */
        public ?array $specificAttributes = null,
        /** @var GuardianData[]|null */
        public ?array $guardians = null,
    ) {}
}
