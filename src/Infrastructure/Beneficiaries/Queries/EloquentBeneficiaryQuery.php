<?php

namespace Infrastructure\Beneficiaries\Queries;

use Application\Beneficiaries\Queries\BeneficiaryQueryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Infrastructure\Beneficiaries\Models\BeneficiaryModel;

final class EloquentBeneficiaryQuery implements BeneficiaryQueryInterface
{
    /**
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<int, BeneficiaryModel>
     */
    public function findAllPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return BeneficiaryModel::with(['familyCard', 'guardians'])
            ->when(
                $filters['type'] ?? null,
                fn ($query, string $type) => $query->where('type', $type),
            )
            ->when(
                $filters['search'] ?? null,
                fn ($query, string $search) => $query->where(function ($q) use ($search): void {
                    $q->where('full_name', 'like', sprintf('%%%s%%', $search))
                        ->orWhere('nik', 'like', sprintf('%%%s%%', $search));
                }),
            )
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}
