<?php

namespace Presentation\Beneficiaries\Http\Controllers;

use App\Http\Controllers\Controller;
use Application\Beneficiaries\DTOs\UpdateBeneficiaryData;
use Application\Beneficiaries\UseCases\UpdateBeneficiaryUseCase;
use Illuminate\Http\JsonResponse;

class UpdateBeneficiaryController extends Controller
{
    public function __construct(
        private readonly UpdateBeneficiaryUseCase $updateBeneficiary,
    ) {}

    public function __invoke(string $id, UpdateBeneficiaryData $request): JsonResponse
    {
        $beneficiary = $this->updateBeneficiary->handle($id, $request);

        return response()->json([
            'message' => 'Klien berhasil diperbarui',
            'data' => $beneficiary,
        ]);
    }
}
