<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class EducationFunding extends Model
{
    use HasUlids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'education_fundings';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'funding_source',
        'amount',
        'currency',
        'funding_start_date',
        'funding_end_date',
        'monthly_tuition_fee',
        'urgent_needs',
        'funding_status',
    ];
}
