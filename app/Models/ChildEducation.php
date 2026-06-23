<?php

namespace App\Models;

use App\Enums\EducationStatus;
use App\Enums\SchoolLevel;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChildEducation extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'child_educations';

    protected $fillable = [
        'foster_child_id',
        'education_status',
        'school_level',
        'school_name',
        'current_grade',
        'major',
        'student_id_number',
    ];

    protected function casts(): array
    {
        return [
            'education_status' => EducationStatus::class,
            'school_level' => SchoolLevel::class,
        ];
    }

    public function fosterChild(): BelongsTo
    {
        return $this->belongsTo(FosterChild::class, 'foster_child_id', 'id');
    }
}
