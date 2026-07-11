<?php

declare(strict_types=1);

namespace Domain\Beneficiaries\Repositories;

use Domain\Beneficiaries\Entities\Guardian;

interface GuardianRepositoryInterface
{
    public function findById(string $id): ?Guardian;

    /** @return Guardian[] */
    public function findByBeneficiaryId(string $beneficiaryId): array;

    public function save(Guardian $guardian): void;

    public function delete(Guardian $guardian): void;

    public function deleteByBeneficiaryId(string $beneficiaryId): void;
}
