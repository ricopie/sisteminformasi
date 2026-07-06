<?php

namespace Modules\Beneficiary\Actions;

use Modules\Beneficiary\Data\UpdateBeneficiaryData;
use Modules\Beneficiary\Enums\BeneficiaryType;
use Modules\Beneficiary\Models\Beneficiary;
use Modules\Beneficiary\Models\FamilyCard;
use Modules\Beneficiary\Models\Guardian;
use Modules\Beneficiary\Repositories\BeneficiaryRepository;
use Modules\Beneficiary\Repositories\FamilyCardRepository;
use Modules\Beneficiary\ValueObjects\Child\ChildAttributes;

class UpdateBeneficiary
{
    public function __construct(
        private readonly BeneficiaryRepository $beneficiaries,
        private readonly FamilyCardRepository $familyCards,
    ) {}

    /** Update beneficiary data */
    public function handle(string $id, UpdateBeneficiaryData $data): Beneficiary
    {
        $beneficiary = $this->beneficiaries->findById($id);

        foreach (['full_name', 'nick_name', 'birth_place', 'birth_date', 'gender'] as $field) {
            if ($data->{$field} !== null) {
                $beneficiary->{$field} = $data->{$field};
            }
        }

        if ($data->family_card !== null) {
            $familyCard = $this->familyCards->findByNumber(
                $data->family_card->family_card_number
            );

            if ($familyCard === null) {
                $familyCard = new FamilyCard;
                $familyCard->family_card_number = $data->family_card->family_card_number;
                $familyCard->head_of_family_name = $data->family_card->head_of_family_name;
                $familyCard->address = $data->family_card->address;
                $this->familyCards->save($familyCard);
            }

            $beneficiary->family_card_id = (string) $familyCard->id;
        }

        if ($data->attributes !== null && $beneficiary->type === BeneficiaryType::CHILD) {
            $beneficiary->setAttribute('attributes', ChildAttributes::fromArray(
                $data->attributes->toArray()
            ));
        }

        $this->beneficiaries->save($beneficiary);

        if ($data->guardians !== null) {
            $beneficiary->guardians()->forceDelete();

            foreach ($data->guardians as $guardianData) {
                $guardian = new Guardian;
                $guardian->beneficiary_id = (string) $beneficiary->id;
                $guardian->person = $guardianData->person;
                $guardian->relationship = $guardianData->relationship;
                $guardian->save();
            }
        }

        return $beneficiary;
    }
}
