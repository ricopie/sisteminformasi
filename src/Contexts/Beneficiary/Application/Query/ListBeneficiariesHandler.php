<?php

declare(strict_types=1);

namespace Copie\Contexts\Beneficiary\Application\Query;

use Copie\Contexts\Beneficiary\Application\BeneficiaryQueryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Handler for listing beneficiaries.
 *
 * Delegates to the BeneficiaryQueryInterface (CQRS read side).
 */
class ListBeneficiariesHandler
{
    public function __construct(
        private readonly BeneficiaryQueryInterface $beneficiaryQuery,
    ) {}

    public function handle(ListBeneficiariesQuery $listBeneficiariesQuery): LengthAwarePaginator
    {
        return $this->beneficiaryQuery->findAllPaginated(
            filters: $listBeneficiariesQuery->filters,
            perPage: $listBeneficiariesQuery->perPage,
        );
    }
}
