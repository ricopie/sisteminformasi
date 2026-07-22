<?php

declare(strict_types=1);

namespace Copie\Contexts\Beneficiary\Application\Handler;

use Copie\Contexts\Beneficiary\Application\BeneficiaryRepositoryInterface;
use Copie\Contexts\Beneficiary\Application\Command\UpdateBeneficiaryCommand;
use Copie\Contexts\Beneficiary\Domain\Beneficiary;
use Copie\Contexts\Beneficiary\Domain\Entities\FamilyCard;
use Copie\Contexts\Beneficiary\Domain\Enums\BeneficiaryType;
use Copie\Contexts\Beneficiary\Domain\Enums\GuardianRelationship;
use Copie\Contexts\Beneficiary\Domain\ValueObjects\Name;
use Copie\Shared\Application\CommandHandler;
use Copie\Shared\Domain\Enums\Gender;
use Copie\Shared\Domain\EventDispatcherInterface;
use Copie\Shared\Domain\Exceptions\EntityNotFoundException;
use Copie\Shared\Domain\ValueObjects\DomainId;
use Copie\Shared\Domain\ValueObjects\Person;

/**
 * Handler for updating an existing beneficiary.
 *
 * Supports partial updates — only provided fields are changed.
 * Guardians and specificAttributes are fully replaced when provided.
 */
class UpdateBeneficiaryHandler extends CommandHandler
{
    public function __construct(
        private readonly BeneficiaryRepositoryInterface $beneficiaryRepository,
        EventDispatcherInterface $eventDispatcher,
    ) {
        parent::__construct($eventDispatcher);
    }

    /**
     * Handle the command to update an existing beneficiary.
     *
     * Supports partial updates — only provided fields are changed.
     *
     * @throws EntityNotFoundException If beneficiary not found
     */
    public function handle(UpdateBeneficiaryCommand $updateBeneficiaryCommand): void
    {
        $domainId = new DomainId($updateBeneficiaryCommand->id);
        $beneficiary = $this->beneficiaryRepository->findById($domainId);

        if (! $beneficiary instanceof Beneficiary) {
            throw EntityNotFoundException::for($updateBeneficiaryCommand->id, 'Beneficiary');
        }

        // Update name if provided
        if ($updateBeneficiaryCommand->firstName !== null || $updateBeneficiaryCommand->lastName !== null) {
            $currentName = $beneficiary->name();
            $beneficiary->rename(
                name: new Name(
                    firstName: $updateBeneficiaryCommand->firstName ?? $currentName->firstName,
                    lastName: $updateBeneficiaryCommand->lastName ?? $currentName->lastName,
                ),
                nickName: $updateBeneficiaryCommand->nickName ?? $beneficiary->nickName(),
            );
        }

        // Update birth info if provided
        if ($updateBeneficiaryCommand->birthPlace !== null || $updateBeneficiaryCommand->birthDate !== null) {
            $beneficiary->updateBirthInfo(
                birthPlace: $updateBeneficiaryCommand->birthPlace ?? $beneficiary->birthPlace(),
                birthDate: $updateBeneficiaryCommand->birthDate ?? $beneficiary->birthDate(),
            );
        }

        // Update gender if provided
        if ($updateBeneficiaryCommand->gender !== null) {
            $beneficiary->changeGender(Gender::from($updateBeneficiaryCommand->gender));
        }

        // Update family card if provided (full replacement of embedded entity)
        if ($updateBeneficiaryCommand->familyCardNumber !== null || $updateBeneficiaryCommand->headOfFamilyName !== null) {
            $currentFC = $beneficiary->familyCard();
            $familyCard = FamilyCard::create(
                number: $updateBeneficiaryCommand->familyCardNumber ?? $currentFC->number(),
                headOfFamilyName: $updateBeneficiaryCommand->headOfFamilyName ?? $currentFC->headOfFamilyName(),
            );
            $beneficiary->assignToFamilyCard($familyCard);
        }

        // Update specific attributes if provided (full replacement)
        if ($updateBeneficiaryCommand->specificAttributes !== null) {
            $type = $updateBeneficiaryCommand->type !== null
                ? BeneficiaryType::from($updateBeneficiaryCommand->type)
                : $beneficiary->type();
            $specificAttributes = $type->createAttributesFrom($updateBeneficiaryCommand->specificAttributes);
            $beneficiary->updateSpecificAttributes($specificAttributes);
        }

        // Replace guardians if provided (full replacement)
        if ($updateBeneficiaryCommand->guardians !== null) {
            $beneficiary->removeAllGuardians();

            foreach ($updateBeneficiaryCommand->guardians as $guardianData) {
                $beneficiary->addGuardian(
                    person: Person::fromArray($guardianData['person'] ?? []),
                    guardianRelationship: GuardianRelationship::from($guardianData['relationship'] ?? 'other'),
                );
            }
        }

        // Update active status if provided
        if ($updateBeneficiaryCommand->isActive !== null) {
            $beneficiary->changeActiveStatus($updateBeneficiaryCommand->isActive);
        }

        $this->beneficiaryRepository->save($beneficiary);
        $this->dispatchEvents($beneficiary);
    }
}
