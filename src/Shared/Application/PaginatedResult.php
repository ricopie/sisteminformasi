<?php

declare(strict_types=1);

namespace Copie\Shared\Application;

/**
 * Framework-agnostic paginated result DTO.
 *
 * Replaces framework-specific paginators in the Application layer.
 * Infrastructure adapters convert framework paginators to this DTO.
 *
 * @template TItem
 */
final readonly class PaginatedResult
{
    /**
     * @param  TItem[]  $items
     */
    public function __construct(
        public array $items,
        public int $total,
        public int $perPage,
        public int $currentPage,
        public int $lastPage,
    ) {
    }
}
