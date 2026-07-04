<?php

namespace Modules\Beneficiary\Models;

use App\Casts\AddressCast;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Beneficiary\ValueObjects\FamilyCardId;
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
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'family_card_number',
        'head_of_family_name',
        'address',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'address' => AddressCast::class,
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
            ->addField('family_card_number')
            ->addBlindIndex('family_card_number', new BlindIndex('family_card_number_index'))
            ->addField('head_of_family_name')
            ->addBlindIndex('head_of_family_name', new BlindIndex('head_of_family_name_index'))
            ->addField('address')
            ->addBlindIndex('address', new BlindIndex('address_index'));
    }

    public function familyMember(): HasMany
    {
        return $this->hasMany(Beneficiary::class, 'family_card_id', 'id');
    }

    public function newUniqueId(): string
    {
        return FamilyCardId::generate()->value;
    }
}
