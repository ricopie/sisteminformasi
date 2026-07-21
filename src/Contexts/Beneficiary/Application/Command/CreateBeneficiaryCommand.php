<?php

declare(strict_types=1);

namespace Copie\Contexts\Beneficiary\Application\Command;

use Copie\Shared\Application\DTO;
use Copie\Shared\Domain\Attributes\NotBlank;

/** Command to create a new beneficiary. */
final class CreateBeneficiaryCommand extends DTO
{
    public function __construct(
        #[NotBlank] public readonly string $nik,
        #[NotBlank] public readonly string $type,
        #[NotBlank] public readonly string $firstName,
        #[NotBlank] public readonly string $lastName,
        public readonly ?string $nickName,
        #[NotBlank] public readonly string $birthPlace,
        #[NotBlank] public readonly string $birthDate,
        #[NotBlank] public readonly string $gender,
        #[NotBlank] public readonly string $familyCardNumber,
        #[NotBlank] public readonly string $headOfFamilyName,
        public readonly ?array $specificAttributes,
    ) {
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): static
    {
        $dto = new self(
            nik: $data['nik'],
            type: $data['type'],
            firstName: $data['firstName'],
            lastName: $data['lastName'],
            nickName: $data['nickName'] ?? null,
            birthPlace: $data['birthPlace'],
            birthDate: $data['birthDate'],
            gender: $data['gender'],
            familyCardNumber: $data['familyCardNumber'],
            headOfFamilyName: $data['headOfFamilyName'],
            specificAttributes: $data['specificAttributes'] ?? null,
        );

        $dto->validate();

        return $dto;
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'nik' => $this->nik,
            'type' => $this->type,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'nickName' => $this->nickName,
            'birthPlace' => $this->birthPlace,
            'birthDate' => $this->birthDate,
            'gender' => $this->gender,
            'familyCardNumber' => $this->familyCardNumber,
            'headOfFamilyName' => $this->headOfFamilyName,
            'specificAttributes' => $this->specificAttributes,
        ];
    }
}
