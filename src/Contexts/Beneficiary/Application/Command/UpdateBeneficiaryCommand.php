<?php

declare(strict_types=1);

namespace Copie\Contexts\Beneficiary\Application\Command;

use Copie\Shared\Application\DTO;

/**
 * Command to update an existing beneficiary.
 *
 * All fields are optional (partial update).
 * Guardians are fully replaced when provided (not incremental).
 */
final class UpdateBeneficiaryCommand extends DTO
{
    public function __construct(
        public readonly string $id,
        public readonly ?string $firstName = null,
        public readonly ?string $lastName = null,
        public readonly ?string $nickName = null,
        public readonly ?string $birthPlace = null,
        public readonly ?string $birthDate = null,
        public readonly ?string $gender = null,
        public readonly ?string $familyCardNumber = null,
        public readonly ?string $headOfFamilyName = null,
        public readonly ?string $type = null,
        public readonly ?array $specificAttributes = null,
        public readonly ?array $guardians = null,
        public readonly ?bool $isActive = null,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): static
    {
        return new self(
            id: $data['id'],
            firstName: $data['firstName'] ?? null,
            lastName: $data['lastName'] ?? null,
            nickName: $data['nickName'] ?? null,
            birthPlace: $data['birthPlace'] ?? null,
            birthDate: $data['birthDate'] ?? null,
            gender: $data['gender'] ?? null,
            familyCardNumber: $data['familyCardNumber'] ?? null,
            headOfFamilyName: $data['headOfFamilyName'] ?? null,
            type: $data['type'] ?? null,
            specificAttributes: $data['specificAttributes'] ?? null,
            guardians: $data['guardians'] ?? null,
            isActive: $data['isActive'] ?? null,
        );
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'nickName' => $this->nickName,
            'birthPlace' => $this->birthPlace,
            'birthDate' => $this->birthDate,
            'gender' => $this->gender,
            'familyCardNumber' => $this->familyCardNumber,
            'headOfFamilyName' => $this->headOfFamilyName,
            'type' => $this->type,
            'specificAttributes' => $this->specificAttributes,
            'guardians' => $this->guardians,
            'isActive' => $this->isActive,
        ];
    }
}
