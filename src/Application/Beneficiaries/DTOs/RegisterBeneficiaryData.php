<?php

declare(strict_types=1);

namespace Application\Beneficiaries\DTOs;

use Application\Beneficiaries\DTOs\Casts\NationalIdentityNumberCast;
use Domain\Beneficiaries\ValueObjects\Enum\BeneficiaryType;
use Domain\Beneficiaries\ValueObjects\Enum\NationalIdentityNumber;
use Shared\ValueObjects\Enum\Gender;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\EnumCast;
use Spatie\LaravelData\Data;

class RegisterBeneficiaryData extends Data
{
    public function __construct(
        #[WithCast(NationalIdentityNumberCast::class)]
        public NationalIdentityNumber $nik,
        #[WithCast(EnumCast::class)]
        public BeneficiaryType $type,
        public string $fullName,
        public ?string $nickName,
        public string $birthPlace,
        public string $birthDate,
        #[WithCast(EnumCast::class)]
        public Gender $gender,
        public FamilyCardData $familyCard,
        /** @var array<mixed>|null */
        public ?array $specificAttributes = null,
        /** @var GuardianData[]|null */
        public ?array $guardians = null,
    ) {}
}
