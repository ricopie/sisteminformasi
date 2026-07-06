<?php

namespace Modules\Beneficiary\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Beneficiary\Casts\BeneficiaryAttributesCast;
use Modules\Beneficiary\Enums\BeneficiaryType;
use Modules\Beneficiary\ValueObjects\BeneficiaryId;
use Modules\Beneficiary\ValueObjects\Child\ChildAttributes;
use ParagonIE\CipherSweet\BlindIndex;
use ParagonIE\CipherSweet\EncryptedRow;
use Spatie\LaravelCipherSweet\Concerns\UsesCipherSweet;
use Spatie\LaravelCipherSweet\Contracts\CipherSweetEncrypted;

/**
 * @property string $id
 * @property string $nik
 * @property BeneficiaryType $type
 * @property string $full_name
 * @property string|null $nick_name
 * @property string $birth_place
 * @property CarbonImmutable|string $birth_date
 * @property string $gender
 * @property ChildAttributes|null $attributes
 * @property string $family_card_id
 * @property Collection|Guardian[] $guardians
 */
class Beneficiary extends Model implements CipherSweetEncrypted
{
    use HasFactory, HasUlids, SoftDeletes, UsesCipherSweet;

    /**
     * The table associated with the model.
     */
    protected $table = 'beneficiary';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'nik',
        'type',
        'full_name',
        'nick_name',
        'birth_place',
        'birth_date',
        'gender',
        'attributes',
        'family_card_id',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'type' => BeneficiaryType::class,
            'birth_date' => 'date',
            'attributes' => BeneficiaryAttributesCast::class,
        ];
    }

    /**
     * Encrypted Fields
     *
     * Each column that should be encrypted should be added below. Each column
     * in the migration should be a `text` type to store the encrypted value.
     *
     * See https://github.com/spatie/laravel-ciphersweet#usage for details.
     */
    public static function configureCipherSweet(EncryptedRow $encryptedRow): void
    {
        $encryptedRow
            ->addField('nik')
            ->addBlindIndex('nik', new BlindIndex('nik_index'))
            ->addField('full_name')
            ->addBlindIndex('full_name', new BlindIndex('full_name_index'))
            ->addOptionalTextField('nick_name')
            ->addField('birth_place')
            ->addField('birth_date');
    }

    public function familyCard(): BelongsTo
    {
        return $this->belongsTo(FamilyCard::class, 'family_card_id', 'id');
    }

    public function guardians(): HasMany
    {
        return $this->hasMany(Guardian::class, 'beneficiary_id', 'id');
    }

    /**
     * Generate a new key for the model.
     */
    public function newUniqueId(): string
    {
        return BeneficiaryId::generate()->value;
    }
}
