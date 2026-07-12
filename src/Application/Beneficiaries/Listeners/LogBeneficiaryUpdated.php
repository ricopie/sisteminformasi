<?php

declare(strict_types=1);

namespace Application\Beneficiaries\Listeners;

use Domain\Beneficiaries\Events\BeneficiaryUpdated;
use Illuminate\Support\Facades\Log;

final class LogBeneficiaryUpdated
{
    public function handle(BeneficiaryUpdated $event): void
    {
        Log::info('[DOMAIN] Beneficiary updated', [
            'id' => (string) $event->beneficiaryId,
        ]);
    }
}
