<?php

namespace Application\Beneficiaries\UseCases;

use Application\Beneficiaries\DTOs\RegisterBeneficiaryData;
use Domain\Beneficiaries\Entities\Beneficiary;
use Domain\Beneficiaries\Entities\FamilyCard;
use Domain\Beneficiaries\Exceptions\BeneficiaryAlreadyExistsException;
use Domain\Beneficiaries\Repositories\BeneficiaryRepositoryInterface;
use Domain\Beneficiaries\Repositories\FamilyCardRepositoryInterface;
use Domain\Beneficiaries\ValueObjects\Child\ChildAttributes;
use Domain\Beneficiaries\ValueObjects\Enum\BeneficiaryType;
use Domain\Beneficiaries\ValueObjects\SpecificAttributes;
use Illuminate\Contracts\Events\Dispatcher;
use Shared\ValueObjects\Address;
use Shared\ValueObjects\Person;

final readonly class RegisterBeneficiaryUseCase
{
    public function __construct(
        private BeneficiaryRepositoryInterface $beneficiaries,
        private FamilyCardRepositoryInterface $familyCards,
        private Dispatcher $events,
    ) {}

    public function handle(RegisterBeneficiaryData $data): Beneficiary
    {
        // Verify NIK uniqueness
        if ($this->beneficiaries->existsByNik($data->nik->value)) {
            throw BeneficiaryAlreadyExistsException::forNik();
        }

        // Find or create FamilyCard
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

        // Build specific attributes based on beneficiary type
        $specificAttributes = $this->resolveSpecificAttributes(
            $data->type,
            $data->specificAttributes,
        );

        // Register beneficiary
        $beneficiary = Beneficiary::register(
            nik: $data->nik,
            type: $data->type,
            fullName: $data->fullName,
            nickName: $data->nickName,
            birthPlace: $data->birthPlace,
            birthDate: $data->birthDate,
            gender: $data->gender,
            familyCardId: $familyCard->id(),
            specificAttributes: $specificAttributes,
        );

        // Add guardians
        foreach ($data->guardians ?? [] as $guardianData) {
            $beneficiary->addGuardian(
                person: Person::fromArray($guardianData->person),
                relationship: $guardianData->relationship,
            );
        }

        // Persist aggregate (Beneficiary + Guardians)
        $this->beneficiaries->save($beneficiary);

        // Dispatch domain events
        foreach ($beneficiary->pullDomainEvents() as $event) {
            $this->events->dispatch($event);
        }

        return $beneficiary;
    }

    /**
     * @param  array<string, mixed>|null  $data
     */
    private function resolveSpecificAttributes(BeneficiaryType $type, ?array $data): ?SpecificAttributes
    {
        if ($data === null) {
            return null;
        }

        return match ($type) {
            BeneficiaryType::CHILD => ChildAttributes::fromArray($data),
            default => null,
        };
    }
}
