<?php

declare(strict_types=1);

namespace Copie\Contexts\Beneficiary\Infrastructure\Laravel\Queries;

use Copie\Contexts\Beneficiary\Application\BeneficiaryQueryInterface;
use Copie\Contexts\Beneficiary\Domain\Beneficiary;
use Copie\Contexts\Beneficiary\Infrastructure\Laravel\Eloquent\BeneficiaryModel;
use Copie\Shared\Application\PaginatedResult;
use Copie\Shared\Domain\ValueObjects\DomainId;

/**
 * Eloquent implementation of BeneficiaryQueryInterface.
 *
 * Read-side (CQRS) query for listing/searching beneficiaries.
 * Handles eager loading and filtering without exposing domain internals.
 */
class EloquentBeneficiaryQuery implements BeneficiaryQueryInterface
{
    public function __construct(
        private readonly BeneficiaryModel $beneficiaryModel,
    ) {}

    /** Find a beneficiary by its unique identifier. */
    public function findById(DomainId $domainId): ?Beneficiary
    {
        $model = $this->beneficiaryModel->newQuery()->find($domainId->value);

        if ($model === null) {
            return null;
        }

        return $model->toDomainEntity();
    }

    /**
     * @param  array<string, mixed>  $filters  Supported: 'type', 'search'
     */
    public function findAllPaginated(array $filters = [], int $perPage = 15): PaginatedResult
    {
        $lengthAwarePaginator = $this->beneficiaryModel->newQuery()
            ->when(
                $filters['type'] ?? null,
                fn ($query, string $type): mixed => $query->where('type', $type),
            )
            ->when(
                $filters['search'] ?? null,
                fn ($query, string $search): mixed => $query->where(function ($q) use ($search): void {
                    $q->where('first_name', 'like', sprintf('%%%s%%', $search))
                        ->orWhere('last_name', 'like', sprintf('%%%s%%', $search))
                        ->orWhere('nik', 'like', sprintf('%%%s%%', $search));
                }),
            )
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return new PaginatedResult(
            items: $lengthAwarePaginator->items(),
            total: $lengthAwarePaginator->total(),
            perPage: $lengthAwarePaginator->perPage(),
            currentPage: $lengthAwarePaginator->currentPage(),
            lastPage: $lengthAwarePaginator->lastPage(),
        );
    }
}
