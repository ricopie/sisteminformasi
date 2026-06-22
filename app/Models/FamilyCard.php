<?php

namespace App\Models;

use App\Traits\HasBlindIndex;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FamilyCard extends Model
{
    use HasUlids, HasBlindIndex, HasFactory;
    protected $table = 'family_card';

    protected array $blindIndexFields = ['family_card_number'];

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

    public function familyMember()
    {
        return $this->hasMany(FosterChild::class, 'family_card_id', 'id');
    }
}
