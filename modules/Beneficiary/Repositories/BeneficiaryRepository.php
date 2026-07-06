<?php

namespace Modules\Beneficiary\Repositories;

use Modules\Beneficiary\Models\Beneficiary;

class BeneficiaryRepository
{
    /** Find beneficiary by NIK via blind index */
    public function findByNik(string $nik): ?Beneficiary
    {
        return Beneficiary::whereBlind('nik', 'nik_index', $nik)->first();
    }

    /** Check if NIK is already registered */
    public function existsByNik(string $nik): bool
    {
        return Beneficiary::whereBlind('nik', 'nik_index', $nik)->exists();
    }

    /** Find beneficiary by ULID */
    public function findById(string $id): Beneficiary
    {
        return Beneficiary::findOrFail($id);
    }

    /** Persist beneficiary to database */
    public function save(Beneficiary $beneficiary): void
    {
        $beneficiary->save();
    }

    /** Soft delete a beneficiary */
    public function delete(Beneficiary $beneficiary): void
    {
        $beneficiary->delete();
    }
}
