<?php

namespace Modules\Beneficiary\Http\Controllers;

use App\Http\Controllers\ModuleController;
use Modules\Beneficiary\Actions\DeleteBeneficiary;

class DeleteBeneficiaryController extends ModuleController
{
    public function __construct(
        private readonly DeleteBeneficiary $deleteBeneficiary,
    ) {}

    public function __invoke(string $id)
    {
        $this->deleteBeneficiary->handle($id);

        return $this->moduleJson(null, 'Klien berhasil dihapus');
    }
}
