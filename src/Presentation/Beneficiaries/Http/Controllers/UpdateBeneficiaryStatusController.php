<?php

declare(strict_types=1);

namespace Presentation\Beneficiaries\Http\Controllers;

use App\Http\Controllers\Controller;
use Application\Beneficiaries\UseCases\UpdateBeneficiaryStatusUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Infrastructure\Security\Services\SecureIdService;
use Shared\Responses\ApiResponse;

class UpdateBeneficiaryStatusController extends Controller
{
    public function __construct(
        private readonly UpdateBeneficiaryStatusUseCase $updateStatus,
    ) {}

    public function __invoke(Request $request, string $secure_beneficiary): JsonResponse
    {
        $validated = $request->validate([
            'is_active' => 'required|boolean',
        ]);

        $service = app(SecureIdService::class);
        $id = $service->decrypt($secure_beneficiary, 'beneficiary');
        $id ??= $secure_beneficiary;

        $result = $this->updateStatus->handle(
            id: $id,
            isActive: (bool) $validated['is_active'],
        );

        $data = $result->toArray();
        $data['secure_id'] = $service->encrypt($result->id()->value, 'beneficiary');

        return ApiResponse::success(
            message: 'Beneficiary status updated successfully.',
            data: $data,
        );
    }
}
