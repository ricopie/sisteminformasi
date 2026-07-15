<?php

declare(strict_types=1);

namespace Copie\Contexts\Beneficiary\Application;

use Copie\Contexts\Beneficiary\Domain\Beneficiary;
use Copie\Contexts\Beneficiary\Domain\ValueObjects\NationalIdentityNumber;
use Copie\Shared\Domain\ValueObjects\DomainId;

/**
 * Repository interface for Beneficiary aggregate.
 *
 * Interface belongs to Application layer.
 * Implementation belongs to Infrastructure layer.
 */
interface BeneficiaryRepositoryInterface
{
    /**
     * Find a beneficiary by its unique identifier.
     */
    public function findById(DomainId $domainId): ?Beneficiary;

    /**
     * Find a beneficiary by its National Identity Number (NIK).
     * Uses blind index for efficient lookup on encrypted data.
     */
    public function findByNik(NationalIdentityNumber $nationalIdentityNumber): ?Beneficiary;

    /**
     * Save a beneficiary (create or update).
     */
    public function save(Beneficiary $beneficiary): void;

    /**
     * Delete a beneficiary by its unique identifier.
     */
    public function delete(DomainId $domainId): void;
}
