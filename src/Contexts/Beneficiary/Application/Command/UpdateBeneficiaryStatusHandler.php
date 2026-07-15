<?php

declare(strict_types=1);

namespace Copie\Contexts\Beneficiary\Application\Command;

use Copie\Contexts\Beneficiary\Application\BeneficiaryRepositoryInterface;
use Copie\Contexts\Beneficiary\Domain\Beneficiary;
use Copie\Shared\Domain\ValueObjects\DomainId;
use RuntimeException;

/**
 * Handler for updating the active status of a beneficiary.
 */
class UpdateBeneficiaryStatusHandler
{
    public function __construct(
        private readonly BeneficiaryRepositoryInterface $beneficiaryRepository,
    ) {}

    public function handle(UpdateBeneficiaryStatusCommand $updateBeneficiaryStatusCommand): void
    {
        $domainId = new DomainId($updateBeneficiaryStatusCommand->id);
        $beneficiary = $this->beneficiaryRepository->findById($domainId);

        if (! $beneficiary instanceof Beneficiary) {
            throw new RuntimeException(sprintf('Beneficiary with ID "%s" not found.', $updateBeneficiaryStatusCommand->id));
        }

        $beneficiary->changeActiveStatus($updateBeneficiaryStatusCommand->isActive);
        $this->beneficiaryRepository->save($beneficiary);
    }
}
