<?php

namespace Application\Beneficiaries\UseCases;

use Application\Beneficiaries\DTOs\FamilyCardData;
use Application\Beneficiaries\DTOs\UpdateBeneficiaryData;
use Domain\Beneficiaries\Entities\Beneficiary;
use Domain\Beneficiaries\Entities\FamilyCard;
use Domain\Beneficiaries\Repositories\BeneficiaryRepositoryInterface;
use Domain\Beneficiaries\Repositories\FamilyCardRepositoryInterface;
use Domain\Beneficiaries\ValueObjects\Child\ChildAttributes;
use Domain\Beneficiaries\ValueObjects\Enum\BeneficiaryType;
use Domain\Beneficiaries\ValueObjects\SpecificAttributes;
use Illuminate\Contracts\Events\Dispatcher;
use Shared\Exceptions\EntityNotFoundException;
use Shared\ValueObjects\Address;
use Shared\ValueObjects\Enum\Gender;
use Shared\ValueObjects\Person;

final readonly class UpdateBeneficiaryUseCase
{
    public function __construct(
        private BeneficiaryRepositoryInterface $beneficiaries,
        private FamilyCardRepositoryInterface $familyCards,
        private Dispatcher $events,
    ) {}

    public function handle(string $id, UpdateBeneficiaryData $data): Beneficiary
    {
        // Find existing beneficiary
        $beneficiary = $this->beneficiaries->findById($id);

        if (! $beneficiary instanceof Beneficiary) {
            throw EntityNotFoundException::forId($id, 'Beneficiary');
        }

        // Update basic fields (only when explicitly provided)
        if ($data->fullName !== null || $data->nickName !== null) {
            $beneficiary->rename(
                fullName: $data->fullName ?? $beneficiary->fullName(),
                nickName: $data->nickName ?? $beneficiary->nickName(),
            );
        }

        if ($data->birthPlace !== null || $data->birthDate !== null) {
            $beneficiary->updateBirthInfo(
                birthPlace: $data->birthPlace ?? $beneficiary->birthPlace(),
                birthDate: $data->birthDate ?? $beneficiary->birthDate(),
            );
        }

        if ($data->gender instanceof Gender) {
            $beneficiary->changeGender($data->gender);
        }

        // Update family card if provided
        if ($data->familyCard instanceof FamilyCardData) {
            $familyCard = $this->familyCards->findByNumber($data->familyCard->number);

            if (! $familyCard instanceof FamilyCard) {
                $familyCard = FamilyCard::register(
                    number: $data->familyCard->number,
                    headOfFamilyName: $data->familyCard->head_of_family_name,
                    address: empty($data->familyCard->address)
                        ? null
                        : Address::fromArray($data->familyCard->address),
                );

                $this->familyCards->save($familyCard);
            }

            $beneficiary->assignToFamilyCard($familyCard->id());
        }

        // Update specific attributes
        if ($data->specificAttributes !== null) {
            $type = $data->type ?? $beneficiary->type();
            $specificAttributes = $this->resolveSpecificAttributes($type, $data->specificAttributes);
            $beneficiary->updateSpecificAttributes($specificAttributes);
        }

        // Replace guardians if provided (full replacement)
        if ($data->guardians !== null) {
            $beneficiary->removeAllGuardians();

            foreach ($data->guardians as $guardianData) {
                $beneficiary->addGuardian(
                    person: Person::fromArray($guardianData->person),
                    relationship: $guardianData->relationship,
                );
            }
        }

        // Persist
        $this->beneficiaries->save($beneficiary);

        // Dispatch domain events
        foreach ($beneficiary->pullDomainEvents() as $event) {
            $this->events->dispatch($event);
        }

        return $beneficiary;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function resolveSpecificAttributes(BeneficiaryType $type, array $data): ?SpecificAttributes
    {
        return match ($type) {
            BeneficiaryType::CHILD => ChildAttributes::fromArray($data),
            default => null,
        };
    }
}
