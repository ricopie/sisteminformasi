<?php

declare(strict_types=1);

namespace Copie\Contexts\Beneficiary\Application\Command;

use Copie\Contexts\Beneficiary\Application\BeneficiaryRepositoryInterface;
use Copie\Contexts\Beneficiary\Domain\Beneficiary;
use Copie\Shared\Application\CommandHandler;
use Copie\Shared\Domain\EventDispatcherInterface;
use Copie\Shared\Domain\Exceptions\EntityNotFoundException;
use Copie\Shared\Domain\ValueObjects\DomainId;

/**
 * Handler for deleting (soft delete) a beneficiary.
 */
class DeleteBeneficiaryHandler extends CommandHandler
{
    public function __construct(
        private readonly BeneficiaryRepositoryInterface $beneficiaryRepository,
        EventDispatcherInterface $eventDispatcher,
    ) {
        parent::__construct($eventDispatcher);
    }

    /**
     * Handle the command to delete (soft delete) a beneficiary.
     *
     *
     * @throws EntityNotFoundException If beneficiary not found
     */
    public function handle(DeleteBeneficiaryCommand $deleteBeneficiaryCommand): void
    {
        $domainId = new DomainId($deleteBeneficiaryCommand->id);
        $beneficiary = $this->beneficiaryRepository->findById($domainId);

        if (! $beneficiary instanceof Beneficiary) {
            throw EntityNotFoundException::for($deleteBeneficiaryCommand->id, 'Beneficiary');
        }

        $beneficiary->markAsDeleted();
        $this->beneficiaryRepository->save($beneficiary);
        $this->dispatchEvents($beneficiary);
    }
}
