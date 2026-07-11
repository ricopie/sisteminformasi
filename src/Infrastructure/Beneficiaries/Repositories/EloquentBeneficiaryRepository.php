<?php

namespace Infrastructure\Beneficiaries\Repositories;

use Carbon\CarbonImmutable;
use DateTimeImmutable;
use Domain\Beneficiaries\Entities\Beneficiary;
use Domain\Beneficiaries\Entities\Guardian;
use Domain\Beneficiaries\Repositories\BeneficiaryRepositoryInterface;
use Domain\Beneficiaries\ValueObjects\Enum\BeneficiaryType;
use Domain\Beneficiaries\ValueObjects\Enum\GuardianRelationship;
use Domain\Beneficiaries\ValueObjects\Enum\NationalIdentityNumber;
use Illuminate\Support\Facades\DB;
use Infrastructure\Beneficiaries\Models\BeneficiaryModel;
use Infrastructure\Beneficiaries\Models\GuardianModel;
use Shared\ValueObjects\DomainId;
use Shared\ValueObjects\Enum\Gender;
use Shared\ValueObjects\Person;

final class EloquentBeneficiaryRepository implements BeneficiaryRepositoryInterface
{
    public function findByNik(string $nik): ?Beneficiary
    {
        $model = BeneficiaryModel::with('guardians')->where('nik', $nik)->first();

        return $model !== null ? $this->toDomain($model) : null;
    }

    public function existsByNik(string $nik): bool
    {
        return BeneficiaryModel::where('nik', $nik)->exists();
    }

    public function findById(string $id): ?Beneficiary
    {
        $model = BeneficiaryModel::with('guardians')->find($id);

        return $model !== null ? $this->toDomain($model) : null;
    }

    public function save(Beneficiary $beneficiary): void
    {
        DB::transaction(function () use ($beneficiary): void {
            $model = BeneficiaryModel::find($beneficiary->id()->value);

            if ($model === null) {
                $model = new BeneficiaryModel;
                $model->id = $beneficiary->id()->value;
            }

            $model->nik = $beneficiary->nik()->value;
            $model->type = $beneficiary->type();
            $model->full_name = $beneficiary->fullName();
            $model->nick_name = $beneficiary->nickName();
            $model->birth_place = $beneficiary->birthPlace();
            $model->birth_date = $beneficiary->birthDate();
            $model->gender = $beneficiary->gender()->value;
            $model->family_card_id = $beneficiary->familyCardId()->value;
            $model->specific_attributes = $beneficiary->specificAttributes();
            $model->save();

            GuardianModel::where('beneficiary_id', $beneficiary->id()->value)->delete();

            foreach ($beneficiary->guardians() as $guardian) {
                $guardianModel = new GuardianModel;
                $guardianModel->id = $guardian->id()->value;
                $guardianModel->beneficiary_id = $guardian->beneficiaryId()->value;
                $guardianModel->person = $guardian->person()->toArray();
                $guardianModel->relationship = $guardian->relationship();
                $guardianModel->save();
            }
        });
    }

    public function delete(Beneficiary $beneficiary): void
    {
        DB::transaction(function () use ($beneficiary): void {
            GuardianModel::where('beneficiary_id', $beneficiary->id()->value)->delete();
            BeneficiaryModel::findOrFail($beneficiary->id()->value)->delete();
        });
    }

    private function toDomain(BeneficiaryModel $model): Beneficiary
    {
        $guardians = array_map(
            fn (GuardianModel $g): Guardian => Guardian::reconstitute(
                id: new DomainId($g->getAttribute('id')),
                createdAt: new DateTimeImmutable((string) $g->getAttribute('created_at')),
                updatedAt: $g->getAttribute('updated_at') !== null
                    ? new DateTimeImmutable((string) $g->getAttribute('updated_at'))
                    : null,
                beneficiaryId: new DomainId($g->getAttribute('beneficiary_id')),
                person: Person::fromArray($g->getAttribute('person')),
                relationship: GuardianRelationship::from($g->getAttribute('relationship')),
            ),
            $model->getRelation('guardians')->all(),
        );

        $beneficiary = Beneficiary::reconstitute(
            id: new DomainId($model->getAttribute('id')),
            createdAt: new DateTimeImmutable((string) $model->getAttribute('created_at')),
            updatedAt: $model->getAttribute('updated_at') !== null
                ? new DateTimeImmutable((string) $model->getAttribute('updated_at'))
                : null,
            nik: new NationalIdentityNumber($model->getAttribute('nik')),
            type: BeneficiaryType::from($model->getAttribute('type')),
            fullName: $model->getAttribute('full_name'),
            nickName: $model->getAttribute('nick_name'),
            birthPlace: $model->getAttribute('birth_place'),
            birthDate: $model->getAttribute('birth_date') instanceof CarbonImmutable
                ? $model->getAttribute('birth_date')->toDateString()
                : (string) $model->getAttribute('birth_date'),
            gender: Gender::from($model->getAttribute('gender')),
            familyCardId: new DomainId($model->getAttribute('family_card_id')),
            specificAttributes: $model->getAttribute('specific_attributes'),
        );

        $beneficiary->restoreGuardians(...$guardians);

        return $beneficiary;
    }
}
