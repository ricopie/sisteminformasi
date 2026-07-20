<?php

declare(strict_types=1);

namespace Copie\Contexts\Beneficiary\Application;

use Copie\Contexts\Beneficiary\Domain\Beneficiary;
use Copie\Contexts\Beneficiary\Domain\ValueObjects\NationalIdentityNumber;
use Copie\Shared\Domain\ValueObjects\DomainId;

/**
 * Repository interface for Beneficiary aggregate (write side).
 */
interface BeneficiaryRepositoryInterface
{
    public function findById(DomainId $domainId): ?Beneficiary;

    /** Uses blind index for efficient encrypted lookup. */
    public function findByNik(NationalIdentityNumber $nationalIdentityNumber): ?Beneficiary;

    public function save(Beneficiary $beneficiary): void;

    public function delete(DomainId $domainId): void;
}
