<?php

namespace App\Models;

use App\Enums\EducationStatus;
use App\Enums\SchoolLevel;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EducationHistory extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'education_histories';

    protected $fillable = [
        'foster_child_id',
        'school_level',
        'school_name',
        'admission_year',
        'graduation_year',
        'status',
        'dropout_reason',
    ];

    protected function casts(): array
    {
        return [
            'status' => EducationStatus::class,
            'school_level' => SchoolLevel::class,
            'admission_year' => 'datetime:Y',
            'graduation_year' => 'datetime:Y',
        ];
    }

    public function fosterChild(): BelongsTo
    {
        return $this->belongsTo(FosterChild::class, 'foster_child_id', 'id');
    }
}
