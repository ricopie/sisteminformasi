<?php

namespace App\Models;

use App\Enums\EducationStatus;
use App\Enums\SchoolLevel;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class ChildEducation extends Model
{
    use HasUlids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'child_education';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'education_status',
        'school_level',
        'school_name',
        'current_grade',
        'major',
        'student_id_number',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'education_status' => EducationStatus::class,
            'school_level' => SchoolLevel::class,
        ];
    }
}
