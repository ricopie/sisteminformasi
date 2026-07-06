<?php

namespace Modules\Beneficiary\Http\Controllers;

use App\Http\Controllers\ModuleController;
use Modules\Beneficiary\Actions\RegisterBeneficiary;
use Modules\Beneficiary\Data\RegisterBeneficiaryData;

class RegisterBeneficiaryController extends ModuleController
{
    public function __construct(
        private readonly RegisterBeneficiary $registerBeneficiary,
    ) {}

    public function __invoke(RegisterBeneficiaryData $request)
    {
        $beneficiary = $this->registerBeneficiary->handle($request);

        return $this->moduleJson($beneficiary, 'Klien berhasil didaftarkan');
    }
}
