<?php

declare(strict_types=1);

namespace Copie\Contexts\Beneficiary\Application\Command;

use Copie\Contexts\Beneficiary\Application\BeneficiaryRepositoryInterface;
use Copie\Contexts\Beneficiary\Domain\Beneficiary;
use Copie\Shared\Domain\ValueObjects\DomainId;
use RuntimeException;

/**
 * Handler for deleting (soft delete) a beneficiary.
 */
class DeleteBeneficiaryHandler
{
    public function __construct(
        private readonly BeneficiaryRepositoryInterface $beneficiaryRepository,
    ) {}

    public function handle(DeleteBeneficiaryCommand $deleteBeneficiaryCommand): void
    {
        $domainId = new DomainId($deleteBeneficiaryCommand->id);
        $beneficiary = $this->beneficiaryRepository->findById($domainId);

        if (! $beneficiary instanceof Beneficiary) {
            throw new RuntimeException(sprintf('Beneficiary with ID "%s" not found.', $deleteBeneficiaryCommand->id));
        }

        $beneficiary->markAsDeleted();
        $this->beneficiaryRepository->save($beneficiary);
    }
}
