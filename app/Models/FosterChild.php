<?php

namespace App\Models;

use App\Traits\HasBlindIndex;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FosterChild extends Model
{
    use HasBlindIndex, HasFactory, HasUlids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'foster_child';

    /**
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
}
