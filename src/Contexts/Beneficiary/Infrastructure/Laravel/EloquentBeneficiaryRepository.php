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

    public function findById(DomainId $domainId): ?Beneficiary
    {
        $model = $this->beneficiaryModel->newQuery()->find($domainId->value);

        if ($model === null) {
            return null;
        }

        return $model->toDomainEntity();
    }

    public function findByNik(NationalIdentityNumber $nationalIdentityNumber): ?Beneficiary
    {
        // Query by blind index for efficient lookup on encrypted data.
        // CipherSweet computes the blind index automatically via the model.
        $model = $this->beneficiaryModel
            ->newQuery()
            ->where('nik', $nationalIdentityNumber->value)
            ->first();

        if ($model === null) {
            return null;
        }

        return $model->toDomainEntity();
    }

    public function save(Beneficiary $beneficiary): void
    {
        $beneficiaryModel = BeneficiaryModel::fromDomainEntity($beneficiary);

        // Use transaction to ensure atomicity
        DB::transaction(function () use ($beneficiaryModel): void {
            // CipherSweet hooks handle encryption on save
            $beneficiaryModel->save();
        });
    }

    public function delete(DomainId $domainId): void
    {
        $model = $this->beneficiaryModel->newQuery()->find($domainId->value);

        if ($model !== null) {
            $model->delete();
        }
    }
}
