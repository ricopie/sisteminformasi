<?php

namespace App\Models;

use App\Traits\HasBlindIndex;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FosterChild extends Model
{
    use HasBlindIndex, HasFactory, HasUlids;

    protected $table = 'foster_child';

    protected array $blindIndexFields = ['nik'];

    protected $fillable = [
        'nik',
        'fullname',
        'nickname',
        'family_card_id',
        'birth_place',
        'birth_date',
        'gender',
    ];

    public function familyCard()
    {
        return $this->belongsTo(FamilyCard::class, 'family_card_id', 'id');
    }
}
