<?php

declare(strict_types=1);

namespace Application\Beneficiaries\DTOs;

use Spatie\LaravelData\Data;

final class UpdateBeneficiaryStatusData extends Data
{
    public function __construct(
        public readonly bool $is_active,
    ) {}
}
