<?php

namespace Presentation\Beneficiaries\Http\Controllers;

use App\Http\Controllers\Controller;
use Application\Beneficiaries\UseCases\GetBeneficiaryUseCase;
use Illuminate\Http\JsonResponse;

class GetBeneficiaryController extends Controller
{
    public function __construct(
        private readonly GetBeneficiaryUseCase $getBeneficiary,
    ) {}

    public function __invoke(string $id): JsonResponse
    {
        $beneficiary = $this->getBeneficiary->handle($id);

        return response()->json([
            'message' => 'OK',
            'data' => $beneficiary->toArray(),
        ]);
    }
}
