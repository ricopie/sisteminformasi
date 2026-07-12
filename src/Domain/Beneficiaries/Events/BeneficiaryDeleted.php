<?php

declare(strict_types=1);

namespace Domain\Beneficiaries\Events;

use Shared\Events\DomainEvent;
use Shared\ValueObjects\DomainId;

final class BeneficiaryDeleted extends DomainEvent
{
    public function __construct(
        public readonly DomainId $beneficiaryId,
    ) {
        parent::__construct();
    }
}
