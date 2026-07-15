<?php

declare(strict_types=1);

namespace Copie\Contexts\Beneficiary\Application\Command;

use Copie\Shared\Application\DTO;

/**
 * Command to delete (soft delete) a beneficiary.
 */
final class DeleteBeneficiaryCommand extends DTO
{
    public function __construct(
        public readonly string $id,
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            id: $data['id'],
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
        ];
    }
}
