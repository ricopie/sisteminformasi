<?php

declare(strict_types=1);

namespace Copie\Contexts\Beneficiary\Domain\Events;

use Copie\Shared\Domain\DomainEvent;
use Copie\Shared\Domain\ValueObjects\DomainId;

/**
 * Domain event fired when a beneficiary is updated.
 */
final class BeneficiaryUpdated extends DomainEvent
{
    public function __construct(
        public readonly DomainId $beneficiaryId,
    ) {
        parent::__construct();
    }
}
