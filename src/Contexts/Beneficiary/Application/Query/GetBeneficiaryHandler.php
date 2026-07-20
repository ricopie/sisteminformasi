<?php

declare(strict_types=1);

namespace Copie\Contexts\Beneficiary\Application\Query;

use Copie\Contexts\Beneficiary\Application\BeneficiaryRepositoryInterface;
use Copie\Contexts\Beneficiary\Domain\Beneficiary;
use Copie\Shared\Domain\ValueObjects\DomainId;

class GetBeneficiaryHandler
{
    public function __construct(
        private readonly BeneficiaryRepositoryInterface $beneficiaryRepository,
    ) {
    }

    public function handle(GetBeneficiaryQuery $getBeneficiaryQuery): ?Beneficiary
    {
        $domainId = new DomainId($getBeneficiaryQuery->id);

        return $this->beneficiaryRepository->findById($domainId);
    }
}
