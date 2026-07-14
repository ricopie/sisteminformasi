<?php

namespace Presentation\Beneficiaries\Http\Controllers;

use App\Http\Controllers\Controller;
use Application\Beneficiaries\UseCases\ListBeneficiariesUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Infrastructure\Security\Services\SecureIdService;

class ListBeneficiariesController extends Controller
{
    public function __construct(
        private readonly ListBeneficiariesUseCase $listBeneficiaries,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $result = $this->listBeneficiaries->handle(
            filters: $request->only(['type', 'search']),
            perPage: (int) $request->integer('per_page', 15),
        );

        $service = app(SecureIdService::class);

        $items = array_map(
            fn (mixed $item): array => [
                ...$item->toArray(),
                'secure_id' => $service->encrypt($item->getAttribute('id'), 'beneficiary'),
            ],
            $result->items(),
        );

        return response()->json([
            'message' => 'OK',
            'data' => $items,
            'meta' => [
                'current_page' => $result->currentPage(),
                'last_page' => $result->lastPage(),
                'per_page' => $result->perPage(),
                'total' => $result->total(),
            ],
        ]);
    }
}
