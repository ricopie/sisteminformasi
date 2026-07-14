<?php

declare(strict_types=1);

namespace Presentation\Beneficiaries\Http\Controllers;

use App\Http\Controllers\Controller;
use Application\Beneficiaries\DTOs\UpdateBeneficiaryData;
use Application\Beneficiaries\UseCases\UpdateBeneficiaryUseCase;
use Illuminate\Http\JsonResponse;
use Infrastructure\Security\Services\SecureIdService;

class UpdateBeneficiaryController extends Controller
{
    public function __construct(
        private readonly UpdateBeneficiaryUseCase $updateBeneficiary,
    ) {}

    public function __invoke(string $secure_beneficiary, UpdateBeneficiaryData $request): JsonResponse
    {
        $service = app(SecureIdService::class);
        $id = $service->decrypt($secure_beneficiary, 'beneficiary');
        $id ??= $secure_beneficiary;

        $beneficiary = $this->updateBeneficiary->handle($id, $request);

        $data = $beneficiary->toArray();
        $data['secure_id'] = $service->encrypt($beneficiary->id()->value, 'beneficiary');

        return response()->json([
            'message' => 'Klien berhasil diperbarui',
            'data' => $data,
        ]);
    }
}
