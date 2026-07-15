<?php

declare(strict_types=1);

namespace Copie\Contexts\Beneficiary\Application;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Query interface for reading beneficiary data (CQRS read side).
 *
 * This interface is separate from BeneficiaryRepositoryInterface (write side).
 * Implementations belong to the Infrastructure layer.
 */
interface BeneficiaryQueryInterface
{
    /**
     * Find beneficiaries with optional filters and pagination.
     *
     * @param  array<string, mixed>  $filters  Supported: 'type', 'search'
     * @return LengthAwarePaginator<int, mixed>
     */
    public function findAllPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator;
}
