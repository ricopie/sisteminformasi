<?php

namespace Application\Beneficiaries\UseCases;

use Domain\Beneficiaries\Entities\Beneficiary;
use Domain\Beneficiaries\Repositories\BeneficiaryRepositoryInterface;
use Illuminate\Contracts\Events\Dispatcher;
use Shared\Exceptions\EntityNotFoundException;

final readonly class DeleteBeneficiaryUseCase
{
    public function __construct(
        private BeneficiaryRepositoryInterface $beneficiaries,
        private Dispatcher $events,
    ) {}

    public function handle(string $id): void
    {
        $beneficiary = $this->beneficiaries->findById($id);

        if (! $beneficiary instanceof Beneficiary) {
            throw EntityNotFoundException::forId($id, 'Beneficiary');
        }

        $beneficiary->markAsDeleted();
        $this->beneficiaries->delete($beneficiary);

        // Dispatch domain events
        foreach ($beneficiary->pullDomainEvents() as $event) {
            $this->events->dispatch($event);
        }
    }
}
