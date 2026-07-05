<?php

namespace Modules\Beneficiary\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Beneficiary\Enums\GuardianRelationship;
use Modules\Beneficiary\ValueObjects\GuardianId;
use ParagonIE\CipherSweet\BlindIndex;
use ParagonIE\CipherSweet\EncryptedRow;
use Spatie\LaravelCipherSweet\Concerns\UsesCipherSweet;
use Spatie\LaravelCipherSweet\Contracts\CipherSweetEncrypted;

/**
 * @property string $id
 * @property string $beneficiary_id
 * @property array $person
 * @property GuardianRelationship $relationship
 * @property Beneficiary $beneficiary
 */
class Guardian extends Model implements CipherSweetEncrypted
{
    use HasFactory, HasUlids, SoftDeletes, UsesCipherSweet;

    /**
     * The table associated with the model.
     */
    protected $table = 'guardians';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['person', 'relationship'];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'person' => 'array',
            'relationship' => GuardianRelationship::class,
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
            ->addField('person')
            ->addBlindIndex('person_index', new BlindIndex('person_index'));
    }

    public function beneficiary(): BelongsTo
    {
        return $this->belongsTo(Beneficiary::class, 'beneficiary_id', 'id');
    }

    /**
     * Generate a new key for the model.
     */
    public function newUniqueId(): string
    {
        return GuardianId::generate()->value;
    }
}
