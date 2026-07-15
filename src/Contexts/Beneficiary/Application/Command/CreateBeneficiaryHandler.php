<?php

declare(strict_types=1);

namespace Copie\Contexts\Beneficiary\Application\Command;

use Copie\Contexts\Beneficiary\Application\BeneficiaryRepositoryInterface;
use Copie\Contexts\Beneficiary\Domain\Beneficiary;
use Copie\Contexts\Beneficiary\Domain\BeneficiaryType;
use Copie\Contexts\Beneficiary\Domain\Entities\FamilyCard;
use Copie\Contexts\Beneficiary\Domain\Exceptions\BeneficiaryAlreadyExistsException;
use Copie\Contexts\Beneficiary\Domain\ValueObjects\ChildAttributes;
use Copie\Contexts\Beneficiary\Domain\ValueObjects\Name;
use Copie\Contexts\Beneficiary\Domain\ValueObjects\NationalIdentityNumber;
use Copie\Contexts\Beneficiary\Domain\ValueObjects\SpecificAttributes;
use Copie\Shared\Domain\Enums\Gender;

/**
 * Handler for creating a new beneficiary.
 *
 * Business rules:
 * 1. NIK must be unique — reject if already exists
 * 2. FamilyCard is created inline (embedded entity)
 * 3. SpecificAttributes are resolved based on beneficiary type
 * 4. Beneficiary is created with all required fields
 */
class CreateBeneficiaryHandler
{
    public function __construct(
        private readonly BeneficiaryRepositoryInterface $beneficiaryRepository,
    ) {}

    public function handle(CreateBeneficiaryCommand $createBeneficiaryCommand): Beneficiary
    {
        // 1. Check if NIK already exists
        $nationalIdentityNumber = new NationalIdentityNumber($createBeneficiaryCommand->nik);
        $existing = $this->beneficiaryRepository->findByNik($nationalIdentityNumber);

        if ($existing instanceof Beneficiary) {
            throw BeneficiaryAlreadyExistsException::forNik($createBeneficiaryCommand->nik);
        }

        // 2. Create FamilyCard
        $familyCard = FamilyCard::create(
            number: $createBeneficiaryCommand->familyCardNumber,
            headOfFamilyName: $createBeneficiaryCommand->headOfFamilyName,
        );

        // 3. Resolve SpecificAttributes based on type
        $beneficiaryType = BeneficiaryType::from($createBeneficiaryCommand->type);
        $specificAttributes = $this->resolveSpecificAttributes(
            $beneficiaryType,
            $createBeneficiaryCommand->specificAttributes,
        );

        // 4. Create Beneficiary
        $beneficiary = Beneficiary::create(
            name: new Name(
                firstName: $createBeneficiaryCommand->firstName,
                lastName: $createBeneficiaryCommand->lastName,
            ),
            nickName: $createBeneficiaryCommand->nickName,
            birthPlace: $createBeneficiaryCommand->birthPlace,
            birthDate: $createBeneficiaryCommand->birthDate,
            gender: Gender::from($createBeneficiaryCommand->gender),
            familyCard: $familyCard,
            specificAttributes: $specificAttributes,
            nik: $nationalIdentityNumber,
            type: $beneficiaryType,
        );

        // 5. Save
        $this->beneficiaryRepository->save($beneficiary);

        return $beneficiary;
    }

    /**
     * Resolve SpecificAttributes from raw array based on beneficiary type.
     *
     * @param  array<string, mixed>|null  $data
     */
    private function resolveSpecificAttributes(BeneficiaryType $beneficiaryType, ?array $data): ?SpecificAttributes
    {
        if ($data === null) {
            return null;
        }

        return match ($beneficiaryType) {
            BeneficiaryType::Child => ChildAttributes::fromArray($data),
            default => null,
        };
    }
}
