<?php

namespace Modules\Beneficiary\Repositories;

use Modules\Beneficiary\Models\FamilyCard;

class FamilyCardRepository
{
    /** Find family card by number via blind index */
    public function findByNumber(string $familyCardNumber): ?FamilyCard
    {
        return FamilyCard::whereBlind(
            'family_card_number_index',
            $familyCardNumber,
            'en'
        )->first();
    }

    /** Persist family card to database */
    public function save(FamilyCard $familyCard): void
    {
        $familyCard->save();
    }
}
