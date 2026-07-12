<?php

declare(strict_types=1);

namespace Domain\Beneficiaries\Events;

use Domain\Beneficiaries\ValueObjects\Enum\BeneficiaryType;
use Domain\Beneficiaries\ValueObjects\Enum\NationalIdentityNumber;
use Shared\Events\DomainEvent;
use Shared\ValueObjects\DomainId;
use Shared\ValueObjects\Enum\Gender;

final class BeneficiaryRegistered extends DomainEvent
{
    public function __construct(
        public readonly DomainId $beneficiaryId,
        public readonly NationalIdentityNumber $nik,
        public readonly BeneficiaryType $type,
        public readonly string $fullName,
        public readonly Gender $gender,
    ) {
        parent::__construct();
    }
}
