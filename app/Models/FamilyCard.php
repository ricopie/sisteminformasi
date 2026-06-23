<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use ParagonIE\CipherSweet\BlindIndex;
use ParagonIE\CipherSweet\EncryptedRow;
use Spatie\LaravelCipherSweet\Concerns\UsesCipherSweet;
use Spatie\LaravelCipherSweet\Contracts\CipherSweetEncrypted;

class FamilyCard extends Model implements CipherSweetEncrypted
{
    use HasFactory, HasUlids, UsesCipherSweet;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'family_card';

    /**
     * [Deprecated]
     * Don't use this property anymore, instead use `configureCipherSweet` method
     * to define encrypted fields and blind index.
     *
     * Define field should be blind indexing
     *
     * @var list<string>
     */
    protected array $blindIndexFields = ['family_card_number'];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'family_card_number',
        'head_of_family_name',
        'address',
        'rt',
        'rw',
        'village',
        'sub_district',
        'city',
        'province',
        'postal_code',
    ];

    /**
     * Get the foster children associated with the family card.
     */
    public function familyMember(): HasMany
    {
        return $this->hasMany(FosterChild::class, 'family_card_id', 'id');
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
            ->addField('family_card_number_encrypted')
            ->addBlindIndex('family_card_number_hash', new BlindIndex('family_card_number_hash'));
    }
}
