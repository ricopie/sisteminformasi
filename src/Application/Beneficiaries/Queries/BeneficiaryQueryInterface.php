<?php

declare(strict_types=1);

namespace Application\Beneficiaries\Queries;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface BeneficiaryQueryInterface
{
    /**
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<int, mixed>
     */
    public function findAllPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator;
}
