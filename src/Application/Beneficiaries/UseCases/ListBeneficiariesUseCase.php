<?php

declare(strict_types=1);

namespace Application\Beneficiaries\UseCases;

use Application\Beneficiaries\Queries\BeneficiaryQueryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final readonly class ListBeneficiariesUseCase
{
    public function __construct(
        private BeneficiaryQueryInterface $query,
    ) {}

    /**
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<int, mixed>
     */
    public function handle(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->query->findAllPaginated($filters, $perPage);
    }
}
