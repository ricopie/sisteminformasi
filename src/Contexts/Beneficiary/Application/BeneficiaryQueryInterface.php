<?php

declare(strict_types=1);

namespace Copie\Contexts\Beneficiary\Application;

use Copie\Contexts\Beneficiary\Domain\Beneficiary;
use Copie\Shared\Application\PaginatedResult;
use Copie\Shared\Domain\ValueObjects\DomainId;

/**
 * Query interface for reading beneficiary data (CQRS read side).
 *
 * This interface is separate from BeneficiaryRepositoryInterface (write side).
 * Implementations belong to the Infrastructure layer.
 */
interface BeneficiaryQueryInterface
{
    /**
     * Find a beneficiary by its unique identifier.
     */
    public function findById(DomainId $domainId): ?Beneficiary;

    /**
     * Find beneficiaries with optional filters and pagination.
     *
     * @param  array<string, mixed>  $filters  Supported: 'type', 'search'
     */
    public function findAllPaginated(array $filters = [], int $perPage = 15): PaginatedResult;
}
