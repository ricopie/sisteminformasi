<?php

declare(strict_types=1);

namespace Copie\Contexts\Beneficiary\Application\Query;

use Copie\Shared\Application\DTO;

final class GetBeneficiaryQuery extends DTO
{
    public function __construct(public readonly string $id)
    {
    }

    public static function fromArray(array $data): static
    {
        return new self($data['id']);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
        ];
    }
}
