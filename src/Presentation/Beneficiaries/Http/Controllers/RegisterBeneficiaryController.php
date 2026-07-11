<?php

namespace Presentation\Beneficiaries\Http\Controllers;

use App\Http\Controllers\Controller;
use Application\Beneficiaries\DTOs\RegisterBeneficiaryData;
use Application\Beneficiaries\UseCases\RegisterBeneficiaryUseCase;
use Illuminate\Http\JsonResponse;

class RegisterBeneficiaryController extends Controller
{
    public function __construct(
        private readonly RegisterBeneficiaryUseCase $registerBeneficiary,
    ) {}

    public function __invoke(RegisterBeneficiaryData $request): JsonResponse
    {
        $beneficiary = $this->registerBeneficiary->handle($request);

        return response()->json([
            'message' => 'Data successfully registered!',
            'data' => $beneficiary,
        ]);
    }
}
