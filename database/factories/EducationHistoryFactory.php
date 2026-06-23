<?php

namespace Database\Factories;

use App\Enums\SchoolLevel;
use App\Models\EducationHistory;
use App\Models\FosterChild;
use Illuminate\Database\Eloquent\Factories\Factory;

class EducationHistoryFactory extends Factory
{
    protected $model = EducationHistory::class;

    public function definition(): array
    {
        return [
            'foster_child_id' => FosterChild::factory(),
            'school_level' => fake()->randomElement(SchoolLevel::cases())->value,
            'school_name' => fake()->company(),
            'admission_year' => fake()->year(),
            'graduation_year' => fake()->optional()->year(),
            'status' => fake()->randomElement(['graduated', 'dropped_out', 'transferred', 'currently_enrolled']),
            'dropout_reason' => fake()->optional()->text(200),
        ];
    }
}
