<?php

declare(strict_types=1);

namespace Copie\Contexts\Beneficiary\Application\Command;

use Copie\Shared\Application\DTO;
use Copie\Shared\Domain\Attributes\NotBlank;

/**
 * Command to delete (soft delete) a beneficiary.
 */
final class DeleteBeneficiaryCommand extends DTO
{
    public function __construct(
        #[NotBlank] public readonly string $id,
    ) {
    }

    public static function fromArray(array $data): static
    {
        $dto = new self(
            id: $data['id'],
        );

        $dto->validate();

        return $dto;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
        ];
    }
}
