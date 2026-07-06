<?php

namespace Modules\Beneficiary\Actions;

use Modules\Beneficiary\Repositories\BeneficiaryRepository;

class DeleteBeneficiary
{
    public function __construct(
        private readonly BeneficiaryRepository $beneficiaries,
    ) {}

    /** Soft delete a beneficiary */
    public function handle(string $id): void
    {
        $beneficiary = $this->beneficiaries->findById($id);
        $this->beneficiaries->delete($beneficiary);
    }
}
