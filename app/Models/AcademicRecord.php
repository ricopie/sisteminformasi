<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AcademicRecord extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'academic_records';

    protected $fillable = [
        'foster_child_id',
        'academic_year',
        'semester',
        'gpa',
        'class_rank',
        'achievements',
        'caregiver_notes',
        'report_card_path',
    ];

    protected function casts(): array
    {
        return [
            'semester' => 'string',
            'gpa' => 'decimal:2',
        ];
    }

    public function fosterChild(): BelongsTo
    {
        return $this->belongsTo(FosterChild::class, 'foster_child_id', 'id');
    }
}
