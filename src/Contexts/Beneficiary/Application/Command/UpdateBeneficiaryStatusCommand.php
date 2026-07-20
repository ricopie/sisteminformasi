<?php

declare(strict_types=1);

namespace Copie\Contexts\Beneficiary\Application\Command;

use Copie\Shared\Application\DTO;

/**
 * Command to update the active status of a beneficiary.
 */
final class UpdateBeneficiaryStatusCommand extends DTO
{
    public function __construct(
        public readonly string $id,
        public readonly bool $isActive,
    ) {
    }

    public static function fromArray(array $data): static
    {
        return new self(
            id: $data['id'],
            isActive: $data['isActive'],
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'isActive' => $this->isActive,
        ];
    }
}
