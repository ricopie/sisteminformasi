<?php

namespace Modules\Beneficiary\Http\Controllers;

use App\Http\Controllers\ModuleController;
use Modules\Beneficiary\Actions\UpdateBeneficiary;
use Modules\Beneficiary\Data\UpdateBeneficiaryData;

class UpdateBeneficiaryController extends ModuleController
{
    public function __construct(
        private readonly UpdateBeneficiary $updateBeneficiary,
    ) {}

    public function __invoke(string $id, UpdateBeneficiaryData $request)
    {
        $beneficiary = $this->updateBeneficiary->handle($id, $request);

        return $this->moduleJson($beneficiary, 'Klien berhasil diperbarui');
    }
}
