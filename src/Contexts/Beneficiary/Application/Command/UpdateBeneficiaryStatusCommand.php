<?php

declare(strict_types=1);

namespace Copie\Contexts\Beneficiary\Application\Command;

use Copie\Shared\Application\DTO;
use Copie\Shared\Domain\Attributes\NotBlank;

/**
 * Command to update the active status of a beneficiary.
 */
final class UpdateBeneficiaryStatusCommand extends DTO
{
    public function __construct(
        #[NotBlank] public readonly string $id,
        public readonly bool $isActive,
    ) {
    }

    public static function fromArray(array $data): static
    {
        $dto = new self(
            id: $data['id'],
            isActive: $data['isActive'],
        );

        $dto->validate();

        return $dto;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'isActive' => $this->isActive,
        ];
    }
}
