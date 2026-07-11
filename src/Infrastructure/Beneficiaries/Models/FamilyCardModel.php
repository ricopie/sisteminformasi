<?php

namespace Infrastructure\Beneficiaries\Models;

use Domain\Beneficiaries\Entities\FamilyCard;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use ParagonIE\CipherSweet\BlindIndex;
use ParagonIE\CipherSweet\EncryptedRow;
use Shared\Casts\AddressCast;
use Shared\ValueObjects\Address;
use Spatie\LaravelCipherSweet\Concerns\UsesCipherSweet;
use Spatie\LaravelCipherSweet\Contracts\CipherSweetEncrypted;

/**
 * @property string $id
 * @property string $family_card_number
 * @property string $head_of_family_name
 * @property Address|array|null $address
 */
class FamilyCardModel extends Model implements CipherSweetEncrypted
{
    use HasFactory;
    use HasUlids;
    use SoftDeletes;
    use UsesCipherSweet;

    protected $table = 'family_card';

    protected $fillable = [
        'family_card_number',
        'head_of_family_name',
        'address',
    ];

    protected function casts(): array
    {
        return [
            'address' => AddressCast::class,
        ];
    }

    public static function configureCipherSweet(EncryptedRow $encryptedRow): void
    {
        $encryptedRow
            ->addField('family_card_number')
            ->addBlindIndex('family_card_number', new BlindIndex('family_card_number_index'))
            ->addField('head_of_family_name')
            ->addBlindIndex('head_of_family_name', new BlindIndex('head_of_family_name_index'))
            ->addOptionalTextField('address')
            ->addBlindIndex('address', new BlindIndex('address_index'));
    }

    public function familyMember(): HasMany
    {
        return $this->hasMany(BeneficiaryModel::class, 'family_card_id', 'id');
    }

    public function newUniqueId(): string
    {
        return FamilyCard::newId()->value;
    }
}
