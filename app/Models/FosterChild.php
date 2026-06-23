<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use ParagonIE\CipherSweet\BlindIndex;
use ParagonIE\CipherSweet\EncryptedRow;
use Spatie\LaravelCipherSweet\Concerns\UsesCipherSweet;
use Spatie\LaravelCipherSweet\Contracts\CipherSweetEncrypted;

class FosterChild extends Model implements CipherSweetEncrypted
{
    use HasFactory, HasUlids, UsesCipherSweet;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'foster_child';

    /**
     * [Deprecated]
     * Don't use this property anymore, instead use `configureCipherSweet` method
     * to define encrypted fields and blind index.
     *
     * Define field should be blind indexing
     *
     * @var list<string>
     */
    protected array $blindIndexFields = ['nik'];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nik',
        'fullname',
        'nickname',
        'family_card_id',
        'birth_place',
        'birth_date',
        'gender',
    ];

    /**
     * Get the family card that the foster child belongs to.
     */
    public function familyCard(): BelongsTo
    {
        return $this->belongsTo(FamilyCard::class, 'family_card_id', 'id');
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
            ->addField('nik_encrypted')
            ->addBlindIndex('nik_hash', new BlindIndex('nik_hash'));
    }
}
