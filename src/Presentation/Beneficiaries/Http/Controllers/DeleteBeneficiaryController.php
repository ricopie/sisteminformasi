<?php

namespace Presentation\Beneficiaries\Http\Controllers;

use App\Http\Controllers\Controller;
use Application\Beneficiaries\UseCases\DeleteBeneficiaryUseCase;
use Illuminate\Http\JsonResponse;

class DeleteBeneficiaryController extends Controller
{
    public function __construct(
        private readonly DeleteBeneficiaryUseCase $deleteBeneficiary,
    ) {}

    public function __invoke(string $id): JsonResponse
    {
        $this->deleteBeneficiary->handle($id);

        return response()->json([
            'message' => 'Klien berhasil dihapus',
            'data' => null,
        ]);
    }
}
