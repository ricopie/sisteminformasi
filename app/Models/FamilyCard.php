<?php

namespace App\Models;

use App\Traits\HasBlindIndex;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FamilyCard extends Model
{
    use HasBlindIndex, HasFactory, HasUlids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'family_card';

    /**
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
}
