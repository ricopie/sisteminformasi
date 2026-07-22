<?php

declare(strict_types=1);

namespace Copie\Contexts\Beneficiary\Application\Handler;

use Copie\Contexts\Beneficiary\Application\BeneficiaryRepositoryInterface;
use Copie\Contexts\Beneficiary\Application\Command\CreateBeneficiaryCommand;
use Copie\Contexts\Beneficiary\Domain\Beneficiary;
use Copie\Contexts\Beneficiary\Domain\Entities\FamilyCard;
use Copie\Contexts\Beneficiary\Domain\Enums\BeneficiaryType;
use Copie\Contexts\Beneficiary\Domain\Exceptions\BeneficiaryAlreadyExistsException;
use Copie\Contexts\Beneficiary\Domain\ValueObjects\Name;
use Copie\Contexts\Beneficiary\Domain\ValueObjects\NationalIdentityNumber;
use Copie\Shared\Application\CommandHandler;
use Copie\Shared\Domain\Enums\Gender;
use Copie\Shared\Domain\EventDispatcherInterface;

/**
 * Handler for creating a new beneficiary.
 *
 * Business rules:
 * 1. NIK must be unique — reject if already exists
 * 2. FamilyCard is created inline (embedded entity)
 * 3. SpecificAttributes are resolved based on beneficiary type
 * 4. Beneficiary is created with all required fields
 */
class CreateBeneficiaryHandler extends CommandHandler
{
    public function __construct(
        private readonly BeneficiaryRepositoryInterface $beneficiaryRepository,
        EventDispatcherInterface $eventDispatcher,
    ) {
        parent::__construct($eventDispatcher);
    }

    /**
     * Handle the command to create a new beneficiary.
     *
     * @throws BeneficiaryAlreadyExistsException If NIK already exists
     */
    public function handle(CreateBeneficiaryCommand $createBeneficiaryCommand): Beneficiary
    {
        $nationalIdentityNumber = new NationalIdentityNumber($createBeneficiaryCommand->nik);
        $existing = $this->beneficiaryRepository->findByNik($nationalIdentityNumber);

        if ($existing instanceof Beneficiary) {
            throw BeneficiaryAlreadyExistsException::forNik($createBeneficiaryCommand->nik);
        }

        $familyCard = FamilyCard::create(
            number: $createBeneficiaryCommand->familyCardNumber,
            headOfFamilyName: $createBeneficiaryCommand->headOfFamilyName,
        );

        $beneficiaryType = BeneficiaryType::from($createBeneficiaryCommand->type);
        $specificAttributes = $beneficiaryType->createAttributesFrom($createBeneficiaryCommand->specificAttributes);

        $beneficiary = Beneficiary::create(
            nationalIdentityNumber: $nationalIdentityNumber,
            beneficiaryType: $beneficiaryType,
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
        );

        $this->beneficiaryRepository->save($beneficiary);
        $this->dispatchEvents($beneficiary);

        return $beneficiary;
    }
}
