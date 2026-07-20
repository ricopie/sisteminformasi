<?php

declare(strict_types=1);

namespace Copie\Contexts\Beneficiary\Application\Command;

use Copie\Shared\Application\DTO;

/** Command to create a new beneficiary. */
final class CreateBeneficiaryCommand extends DTO
{
    public function __construct(
        public readonly string $nik,
        public readonly string $type,
        public readonly string $firstName,
        public readonly string $lastName,
        public readonly ?string $nickName,
        public readonly string $birthPlace,
        public readonly string $birthDate,
        public readonly string $gender,
        public readonly string $familyCardNumber,
        public readonly string $headOfFamilyName,
        public readonly ?array $specificAttributes,
    ) {
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): static
    {
        return new self(
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
