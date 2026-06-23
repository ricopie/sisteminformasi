<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EducationFunding extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'education_fundings';

    protected $fillable = [
        'foster_child_id',
        'funding_source',
        'amount',
        'currency',
        'funding_start_date',
        'funding_end_date',
        'monthly_tuition_fee',
        'urgent_needs',
        'funding_status',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'funding_start_date' => 'date',
            'funding_end_date' => 'date',
            'monthly_tuition_fee' => 'decimal:2',
        ];
    }

    public function fosterChild(): BelongsTo
    {
        return $this->belongsTo(FosterChild::class, 'foster_child_id', 'id');
    }
}
