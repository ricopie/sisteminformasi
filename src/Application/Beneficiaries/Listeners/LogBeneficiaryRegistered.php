<?php

declare(strict_types=1);

namespace Application\Beneficiaries\Listeners;

use Domain\Beneficiaries\Events\BeneficiaryRegistered;
use Illuminate\Support\Facades\Log;

final class LogBeneficiaryRegistered
{
    public function handle(BeneficiaryRegistered $event): void
    {
        Log::info('[DOMAIN] Beneficiary registered', [
            'id' => (string) $event->beneficiaryId,
            'nik' => $event->nik->value,
            'type' => $event->type->value,
            'name' => $event->fullName,
        ]);
    }
}
