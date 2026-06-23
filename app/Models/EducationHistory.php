<?php

namespace App\Models;

use App\Enums\EducationStatus;
use App\Enums\SchoolLevel;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class EducationHistory extends Model
{
    use HasUlids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'education_histories';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'school_level',
        'school_name',
        'admission_year',
        'graduation_year',
        'status',
        'dropout_reason',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => EducationStatus::class,
            'school_level' => SchoolLevel::class,
            'admission_year' => 'datetime:Y',
            'graduation_year' => 'datetime:Y',
        ];
    }
}
