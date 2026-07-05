<?php

namespace Modules\Beneficiary\Repositories;

use Modules\Beneficiary\Models\Beneficiary;

class BeneficiaryRepository
{
    /** Find beneficiary by NIK via blind index */
    public function findByNik(string $nik): ?Beneficiary
    {
        return Beneficiary::whereBlind('nik_index', $nik, 'en')->first();
    }

    /** Check if NIK is already registered */
    public function existsByNik(string $nik): bool
    {
        return Beneficiary::whereBlind('nik_index', $nik, 'en')->exists();
    }

    /** Persist beneficiary to database */
    public function save(Beneficiary $beneficiary): void
    {
        $beneficiary->save();
    }
}
