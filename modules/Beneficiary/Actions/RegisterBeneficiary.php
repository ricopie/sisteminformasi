<?php

namespace Modules\Beneficiary\Actions;

use Modules\Beneficiary\Data\RegisterBeneficiaryData;
use Modules\Beneficiary\Enums\BeneficiaryType;
use Modules\Beneficiary\Exceptions\BeneficiaryAlreadyExistsException;
use Modules\Beneficiary\Models\Beneficiary;
use Modules\Beneficiary\Models\FamilyCard;
use Modules\Beneficiary\Models\Guardian;
use Modules\Beneficiary\Repositories\BeneficiaryRepository;
use Modules\Beneficiary\Repositories\FamilyCardRepository;
use Modules\Beneficiary\ValueObjects\Child\ChildAttributes;

class RegisterBeneficiary
{
    public function __construct(
        private readonly BeneficiaryRepository $beneficiaries,
        private readonly FamilyCardRepository $familyCards,
    ) {}

    /** Execute the registration flow */
    public function handle(RegisterBeneficiaryData $data): Beneficiary
    {
        $this->ensureNikIsUnique($data->nik);

        $familyCard = $this->findOrCreateFamilyCard($data);

        $beneficiary = new Beneficiary;
        $beneficiary->nik = $data->nik;
        $beneficiary->type = $data->type;
        $beneficiary->full_name = $data->full_name;
        $beneficiary->nick_name = $data->nick_name;
        $beneficiary->birth_place = $data->birth_place;
        $beneficiary->birth_date = $data->birth_date;
        $beneficiary->gender = $data->gender;
        $beneficiary->family_card_id = (string) $familyCard->id;

        if ($data->type === BeneficiaryType::CHILD && $data->attributes !== null) {
            $beneficiary->setAttribute('attributes', ChildAttributes::fromArray(
                $data->attributes->toArray()
            ));
        }

        $this->beneficiaries->save($beneficiary);

        $this->createGuardians($data, $beneficiary);

        return $beneficiary;
    }

    /** Ensure the NIK is not already registered */
    private function ensureNikIsUnique(string $nik): void
    {
        if ($this->beneficiaries->existsByNik($nik)) {
            throw new BeneficiaryAlreadyExistsException($nik);
        }
    }

    /** Find existing family card by number or create a new one */
    private function findOrCreateFamilyCard(RegisterBeneficiaryData $data): FamilyCard
    {
        $existing = $this->familyCards->findByNumber(
            $data->family_card->family_card_number
        );

        if ($existing !== null) {
            return $existing;
        }

        $familyCard = new FamilyCard;
        $familyCard->family_card_number = $data->family_card->family_card_number;
        $familyCard->head_of_family_name = $data->family_card->head_of_family_name;
        $familyCard->address = $data->family_card->address;
        $this->familyCards->save($familyCard);

        return $familyCard;
    }

    /** Create guardian records for the beneficiary */
    private function createGuardians(RegisterBeneficiaryData $data, Beneficiary $beneficiary): void
    {
        if ($data->guardians === null) {
            return;
        }

        foreach ($data->guardians as $guardianData) {
            $guardian = new Guardian;
            $guardian->beneficiary_id = (string) $beneficiary->id;
            $guardian->person = $guardianData->person;
            $guardian->relationship = $guardianData->relationship;
            $guardian->save();
        }
    }
}
