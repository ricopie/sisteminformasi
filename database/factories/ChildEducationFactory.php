<?php

namespace Database\Factories;

use App\Enums\SchoolLevel;
use App\Models\ChildEducation;
use App\Models\FosterChild;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChildEducationFactory extends Factory
{
    protected $model = ChildEducation::class;

    public function definition(): array
    {
        return [
            'foster_child_id' => FosterChild::factory(),
            'education_status' => fake()->randomElement(['enrolled', 'dropped_out', 'not_enrolled', 'graduated']),
            'school_level' => fake()->randomElement(SchoolLevel::cases())->value,
            'school_name' => fake()->company(),
            'current_grade' => 'Grade ' . fake()->numberBetween(1, 12),
            'major' => fake()->randomElement(['Science', 'Social Studies', 'Computer Science', null]),
            'student_id_number' => fake()->numerify('###########'),
        ];
    }
}
