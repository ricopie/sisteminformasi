<?php

declare(strict_types=1);

namespace Application\Beneficiaries\UseCases;

use Domain\Beneficiaries\Entities\Beneficiary;
use Domain\Beneficiaries\Repositories\BeneficiaryRepositoryInterface;
use Illuminate\Contracts\Events\Dispatcher;
use Shared\Exceptions\EntityNotFoundException;

final readonly class UpdateBeneficiaryStatusUseCase
{
    public function __construct(
        private BeneficiaryRepositoryInterface $beneficiaries,
        private Dispatcher $events,
    ) {}

    /**
     * Update the active status of a beneficiary.
     */
    public function handle(string $id, bool $isActive): Beneficiary
    {
        $beneficiary = $this->beneficiaries->findById($id);

        if (! $beneficiary instanceof Beneficiary) {
            throw EntityNotFoundException::forId($id, 'Beneficiary');
        }

        $beneficiary->changeActiveStatus($isActive);

        $this->beneficiaries->save($beneficiary);

        // Dispatch domain events
        foreach ($beneficiary->pullDomainEvents() as $event) {
            $this->events->dispatch($event);
        }

        return $beneficiary;
    }
}
