<?php

declare(strict_types=1);

namespace Presentation\Beneficiaries\Http\Controllers;

use App\Http\Controllers\Controller;
use Application\Beneficiaries\UseCases\DeleteBeneficiaryUseCase;
use Illuminate\Http\JsonResponse;
use Infrastructure\Security\Services\SecureIdService;

class DeleteBeneficiaryController extends Controller
{
    public function __construct(
        private readonly DeleteBeneficiaryUseCase $deleteBeneficiary,
    ) {}

    public function __invoke(string $secure_beneficiary): JsonResponse
    {
        $service = app(SecureIdService::class);
        $id = $service->decrypt($secure_beneficiary, 'beneficiary');
        $id ??= $secure_beneficiary;

        $this->deleteBeneficiary->handle($id);

        return response()->json([
            'message' => 'Klien berhasil dihapus',
            'data' => null,
        ]);
    }
}
