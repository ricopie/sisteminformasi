<?php

declare(strict_types=1);

namespace Application\Beneficiaries\Listeners;

use Domain\Beneficiaries\Events\BeneficiaryDeleted;
use Illuminate\Support\Facades\Log;

final class LogBeneficiaryDeleted
{
    public function handle(BeneficiaryDeleted $event): void
    {
        Log::info('[DOMAIN] Beneficiary deleted', [
            'id' => (string) $event->beneficiaryId,
        ]);
    }
}
