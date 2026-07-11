<?php

namespace Infrastructure\Beneficiaries\Repositories;

use DateTimeImmutable;
use Domain\Beneficiaries\Entities\Guardian;
use Domain\Beneficiaries\Repositories\GuardianRepositoryInterface;
use Domain\Beneficiaries\ValueObjects\Enum\GuardianRelationship;
use Infrastructure\Beneficiaries\Models\GuardianModel;
use Shared\ValueObjects\DomainId;
use Shared\ValueObjects\Person;

final class EloquentGuardianRepository implements GuardianRepositoryInterface
{
    public function findById(string $id): ?Guardian
    {
        $model = GuardianModel::find($id);

        return $model !== null ? $this->toDomain($model) : null;
    }

    /** @return Guardian[] */
    public function findByBeneficiaryId(string $beneficiaryId): array
    {
        return GuardianModel::where('beneficiary_id', $beneficiaryId)
            ->get()
            ->map(fn (GuardianModel $model): Guardian => $this->toDomain($model))
            ->all();
    }

    public function save(Guardian $guardian): void
    {
        $model = GuardianModel::find($guardian->id()->value);

        if ($model === null) {
            $model = new GuardianModel;
            $model->id = $guardian->id()->value;
        }

        $model->beneficiary_id = $guardian->beneficiaryId()->value;
        $model->person = $guardian->person()->toArray();
        $model->relationship = $guardian->relationship();
        $model->save();
    }

    public function delete(Guardian $guardian): void
    {
        GuardianModel::findOrFail($guardian->id()->value)->delete();
    }

    public function deleteByBeneficiaryId(string $beneficiaryId): void
    {
        GuardianModel::where('beneficiary_id', $beneficiaryId)->delete();
    }

    private function toDomain(GuardianModel $model): Guardian
    {
        return Guardian::reconstitute(
            id: new DomainId($model->getAttribute('id')),
            createdAt: new DateTimeImmutable((string) $model->getAttribute('created_at')),
            updatedAt: $model->getAttribute('updated_at') !== null
                ? new DateTimeImmutable((string) $model->getAttribute('updated_at'))
                : null,
            beneficiaryId: new DomainId($model->getAttribute('beneficiary_id')),
            person: Person::fromArray($model->getAttribute('person')),
            relationship: GuardianRelationship::from($model->getAttribute('relationship')),
        );
    }
}
