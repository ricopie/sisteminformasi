<?php

namespace Presentation\Beneficiaries\Http\Controllers;

use App\Http\Controllers\Controller;
use Application\Beneficiaries\DTOs\RegisterBeneficiaryData;
use Application\Beneficiaries\UseCases\RegisterBeneficiaryUseCase;
use Illuminate\Http\JsonResponse;
use Infrastructure\Security\Services\SecureIdService;

class RegisterBeneficiaryController extends Controller
{
    public function __construct(
        private readonly RegisterBeneficiaryUseCase $registerBeneficiary,
    ) {}

    public function __invoke(RegisterBeneficiaryData $request): JsonResponse
    {
        $beneficiary = $this->registerBeneficiary->handle($request);

        $data = $beneficiary->toArray();
        $data['secure_id'] = app(SecureIdService::class)
            ->encrypt($beneficiary->id()->value, 'beneficiary');

        return response()->json([
            'message' => 'Data successfully registered!',
            'data' => $data,
        ]);
    }
}
