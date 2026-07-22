<?php

declare(strict_types=1);

namespace Copie\Contexts\Beneficiary\Infrastructure\Laravel;

use Copie\Contexts\Beneficiary\Application\BeneficiaryRepositoryInterface;
use Copie\Contexts\Beneficiary\Domain\Beneficiary;
use Copie\Contexts\Beneficiary\Domain\ValueObjects\NationalIdentityNumber;
use Copie\Contexts\Beneficiary\Infrastructure\Laravel\Eloquent\BeneficiaryModel;
use Copie\Shared\Domain\ValueObjects\DomainId;
use Illuminate\Support\Facades\DB;

/**
 * Eloquent implementation of BeneficiaryRepositoryInterface.
 *
 * This adapter maps between the domain Beneficiary aggregate root
 * and the Eloquent model, handling persistence and encryption.
 */
class EloquentBeneficiaryRepository implements BeneficiaryRepositoryInterface
{
    public function __construct(
        private readonly BeneficiaryModel $beneficiaryModel,
    ) {}

    /** Find a beneficiary by its unique identifier. */
    public function findById(DomainId $domainId): ?Beneficiary
    {
        $model = $this->beneficiaryModel->newQuery()->find($domainId->value);

        if ($model === null) {
            return null;
        }

        return $model->toDomainEntity();
    }

    /** Find a beneficiary by NIK. CipherSweet transparently decrypts for query resolution. */
    public function findByNik(NationalIdentityNumber $nationalIdentityNumber): ?Beneficiary
    {
        // CipherSweet transparently decrypts encrypted columns during query resolution.
        $model = $this->beneficiaryModel
            ->newQuery()
            ->where('nik', $nationalIdentityNumber->value)
            ->first();

        if ($model === null) {
            return null;
        }

        return $model->toDomainEntity();
    }

    /** Save a beneficiary (create or update) within a transaction. */
    public function save(Beneficiary $beneficiary): void
    {
        $beneficiaryModel = BeneficiaryModel::fromDomainEntity($beneficiary);

        // Use transaction to ensure atomicity
        DB::transaction(function () use ($beneficiaryModel): void {
            $beneficiaryModel->save();
        });
    }

    /** Delete a beneficiary by its unique identifier. */
    public function delete(DomainId $domainId): void
    {
        $model = $this->beneficiaryModel->newQuery()->find($domainId->value);

        if ($model !== null) {
            $model->delete();
        }
    }
}
