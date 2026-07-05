<?php

namespace Modules\Beneficiary\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Beneficiary\Casts\BeneficiaryAttributesCast;
use Modules\Beneficiary\Enums\BeneficiaryType;
use Modules\Beneficiary\ValueObjects\BeneficiaryId;
use ParagonIE\CipherSweet\BlindIndex;
use ParagonIE\CipherSweet\EncryptedRow;
use Spatie\LaravelCipherSweet\Concerns\UsesCipherSweet;
use Spatie\LaravelCipherSweet\Contracts\CipherSweetEncrypted;

class Beneficiary extends Model implements CipherSweetEncrypted
{
    use HasFactory, HasUlids, SoftDeletes, UsesCipherSweet;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'beneficiary';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
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
     *
     * @return array<string, string>
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
            ->addBlindIndex('nik_index', new BlindIndex('nik_index'));
    }

    public function familyCard(): BelongsTo
    {
        return $this->belongsTo(FamilyCard::class, 'family_card_id', 'id');
    }

    public function newUniqueId(): string
    {
        return BeneficiaryId::generate()->value;
    }
}
