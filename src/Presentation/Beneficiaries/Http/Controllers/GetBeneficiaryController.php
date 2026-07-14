<?php

declare(strict_types=1);

namespace Presentation\Beneficiaries\Http\Controllers;

use App\Http\Controllers\Controller;
use Application\Beneficiaries\UseCases\GetBeneficiaryUseCase;
use Illuminate\Http\JsonResponse;
use Infrastructure\Security\Services\SecureIdService;

class GetBeneficiaryController extends Controller
{
    public function __construct(
        private readonly GetBeneficiaryUseCase $getBeneficiary,
    ) {}

    public function __invoke(string $secure_beneficiary): JsonResponse
    {
        $service = app(SecureIdService::class);
        $id = $service->decrypt($secure_beneficiary, 'beneficiary');
        $id ??= $secure_beneficiary;

        $beneficiary = $this->getBeneficiary->handle($id);

        $data = $beneficiary->toArray();
        $data['secure_id'] = $service->encrypt($beneficiary->id()->value, 'beneficiary');

        return response()->json([
            'message' => 'OK',
            'data' => $data,
        ]);
    }
}
