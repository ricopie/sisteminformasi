<?php

declare(strict_types=1);

namespace Application\Beneficiaries\UseCases;

use Domain\Beneficiaries\Entities\Beneficiary;
use Domain\Beneficiaries\Repositories\BeneficiaryRepositoryInterface;
use Shared\Exceptions\EntityNotFoundException;

final readonly class GetBeneficiaryUseCase
{
    public function __construct(
        private BeneficiaryRepositoryInterface $beneficiaries,
    ) {}

    public function handle(string $id): Beneficiary
    {
        $beneficiary = $this->beneficiaries->findById($id);

        if (! $beneficiary instanceof Beneficiary) {
            throw EntityNotFoundException::forId($id, 'Beneficiary');
        }

        return $beneficiary;
    }
}
