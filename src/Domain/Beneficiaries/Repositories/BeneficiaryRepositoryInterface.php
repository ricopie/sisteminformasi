<?php

declare(strict_types=1);

namespace Domain\Beneficiaries\Repositories;

use Domain\Beneficiaries\Entities\Beneficiary;

interface BeneficiaryRepositoryInterface
{
    public function findByNik(string $nik): ?Beneficiary;

    public function existsByNik(string $nik): bool;

    public function findById(string $id): ?Beneficiary;

    public function save(Beneficiary $beneficiary): void;

    public function delete(Beneficiary $beneficiary): void;
}
