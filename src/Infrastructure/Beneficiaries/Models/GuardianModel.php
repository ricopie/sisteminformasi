<?php

namespace Infrastructure\Beneficiaries\Models;

use Database\Factories\GuardianModelFactory;
use Domain\Beneficiaries\Entities\Guardian;
use Domain\Beneficiaries\ValueObjects\Enum\GuardianRelationship;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use ParagonIE\CipherSweet\BlindIndex;
use ParagonIE\CipherSweet\EncryptedRow;
use Spatie\LaravelCipherSweet\Concerns\UsesCipherSweet;
use Spatie\LaravelCipherSweet\Contracts\CipherSweetEncrypted;

/**
 * @property string $id
 * @property string $beneficiary_id
 * @property array $person
 * @property GuardianRelationship $relationship
 * @property BeneficiaryModel $beneficiary
 */
class GuardianModel extends Model implements CipherSweetEncrypted
{
    use HasFactory;
    use HasUlids;
    use SoftDeletes;
    use UsesCipherSweet;

    protected $table = 'guardians';

    protected $fillable = ['person', 'relationship'];

    protected function casts(): array
    {
        return [
            'person' => 'array',
            'relationship' => GuardianRelationship::class,
        ];
    }

    public static function configureCipherSweet(EncryptedRow $encryptedRow): void
    {
        $encryptedRow
            ->addField('person')
            ->addBlindIndex('person', new BlindIndex('person_index'));
    }

    protected static function newFactory(): GuardianModelFactory
    {
        return GuardianModelFactory::new();
    }

    public function beneficiary(): BelongsTo
    {
        return $this->belongsTo(BeneficiaryModel::class, 'beneficiary_id', 'id');
    }

    public function newUniqueId(): string
    {
        return Guardian::newId()->value;
    }
}
