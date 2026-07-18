<?php

declare(strict_types=1);

namespace Copie\Contexts\Beneficiary\Domain\Events;

use Copie\Contexts\Beneficiary\Domain\Enums\BeneficiaryType;
use Copie\Contexts\Beneficiary\Domain\ValueObjects\Name;
use Copie\Shared\Domain\DomainEvent;
use Copie\Shared\Domain\ValueObjects\DomainId;

final class BeneficiaryCreated extends DomainEvent
{
    public function __construct(
        public readonly DomainId $beneficiaryId,
        public readonly Name $name,
        public readonly BeneficiaryType $type,
    ) {
        parent::__construct();
    }
}
