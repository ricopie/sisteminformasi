<?php

declare(strict_types=1);

namespace Copie\Contexts\Beneficiary\Application\Query;

use Copie\Shared\Application\DTO;

/**
 * Query to list beneficiaries with optional filters and pagination.
 */
final class ListBeneficiariesQuery extends DTO
{
    /**
     * @param  array<string, mixed>  $filters  Supported: 'type', 'search'
     */
    public function __construct(
        public readonly array $filters = [],
        public readonly int $perPage = 15,
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            filters: $data['filters'] ?? [],
            perPage: $data['perPage'] ?? 15,
        );
    }

    public function toArray(): array
    {
        return [
            'filters' => $this->filters,
            'perPage' => $this->perPage,
        ];
    }
}
