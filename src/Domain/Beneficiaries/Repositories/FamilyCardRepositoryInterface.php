<?php

declare(strict_types=1);

namespace Domain\Beneficiaries\Repositories;

use Domain\Beneficiaries\Entities\FamilyCard;

interface FamilyCardRepositoryInterface
{
    public function findById(string $id): ?FamilyCard;

    public function findByNumber(string $number): ?FamilyCard;

    public function save(FamilyCard $familyCard): void;

    public function delete(FamilyCard $familyCard): void;
}
