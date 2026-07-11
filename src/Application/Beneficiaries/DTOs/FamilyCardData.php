<?php

declare(strict_types=1);

namespace Application\Beneficiaries\DTOs;

use Spatie\LaravelData\Data;

class FamilyCardData extends Data
{
    public function __construct(
        public string $family_card_number,
        public string $head_of_family_name,
        /** @var array{street: string, rt: string, rw: string, village: string, district: string, city: string, province: string, postal_code: string} */
        public array $address,
    ) {}
}
