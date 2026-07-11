<?php

namespace Infrastructure\Beneficiaries\Repositories;

use DateTimeImmutable;
use Domain\Beneficiaries\Entities\FamilyCard;
use Domain\Beneficiaries\Repositories\FamilyCardRepositoryInterface;
use Infrastructure\Beneficiaries\Models\FamilyCardModel;
use Shared\ValueObjects\Address;
use Shared\ValueObjects\DomainId;

final class EloquentFamilyCardRepository implements FamilyCardRepositoryInterface
{
    public function findById(string $id): ?FamilyCard
    {
        $model = FamilyCardModel::find($id);

        return $model !== null ? $this->toDomain($model) : null;
    }

    public function findByNumber(string $familyCardNumber): ?FamilyCard
    {
        $model = FamilyCardModel::where('family_card_number', $familyCardNumber)->first();

        return $model !== null ? $this->toDomain($model) : null;
    }

    public function save(FamilyCard $familyCard): void
    {
        $model = FamilyCardModel::find($familyCard->id()->value);

        if ($model === null) {
            $model = new FamilyCardModel;
            $model->id = $familyCard->id()->value;
        }

        $model->family_card_number = $familyCard->familyCardNumber();
        $model->head_of_family_name = $familyCard->headOfFamilyName();
        $model->address = $familyCard->address()?->toArray();
        $model->save();
    }

    public function delete(FamilyCard $familyCard): void
    {
        FamilyCardModel::findOrFail($familyCard->id()->value)->delete();
    }

    private function toDomain(FamilyCardModel $model): FamilyCard
    {
        return FamilyCard::reconstitute(
            id: new DomainId($model->getAttribute('id')),
            createdAt: new DateTimeImmutable((string) $model->getAttribute('created_at')),
            updatedAt: $model->getAttribute('updated_at') !== null
                ? new DateTimeImmutable((string) $model->getAttribute('updated_at'))
                : null,
            familyCardNumber: $model->getAttribute('family_card_number'),
            headOfFamilyName: $model->getAttribute('head_of_family_name'),
            address: $model->getAttribute('address') instanceof Address
                ? $model->getAttribute('address')
                : null,
        );
    }
}
