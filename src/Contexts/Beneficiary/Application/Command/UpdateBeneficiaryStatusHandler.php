<?php

declare(strict_types=1);

namespace Copie\Contexts\Beneficiary\Application\Command;

use Copie\Contexts\Beneficiary\Application\BeneficiaryRepositoryInterface;
use Copie\Contexts\Beneficiary\Domain\Beneficiary;
use Copie\Shared\Application\CommandHandler;
use Copie\Shared\Domain\EventDispatcherInterface;
use Copie\Shared\Domain\ValueObjects\DomainId;
use RuntimeException;

/**
 * Handler for updating the active status of a beneficiary.
 */
class UpdateBeneficiaryStatusHandler extends CommandHandler
{
    public function __construct(
        private readonly BeneficiaryRepositoryInterface $beneficiaryRepository,
        EventDispatcherInterface $eventDispatcher,
    ) {
        parent::__construct($eventDispatcher);
    }

    /**
     * Handle the command to update active status.
     *
     *
     * @throws RuntimeException If beneficiary not found
     */
    public function handle(UpdateBeneficiaryStatusCommand $updateBeneficiaryStatusCommand): void
    {
        $domainId = new DomainId($updateBeneficiaryStatusCommand->id);
        $beneficiary = $this->beneficiaryRepository->findById($domainId);

        if (! $beneficiary instanceof Beneficiary) {
            throw new RuntimeException(sprintf('Beneficiary with ID "%s" not found.', $updateBeneficiaryStatusCommand->id));
        }

        $beneficiary->changeActiveStatus($updateBeneficiaryStatusCommand->isActive);
        $this->beneficiaryRepository->save($beneficiary);
        $this->dispatchEvents($beneficiary);
    }
}
