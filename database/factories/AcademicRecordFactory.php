<?php

namespace Database\Factories;

use App\Models\AcademicRecord;
use App\Models\FosterChild;
use Illuminate\Database\Eloquent\Factories\Factory;

class AcademicRecordFactory extends Factory
{
    protected $model = AcademicRecord::class;

    public function definition(): array
    {
        return [
            'foster_child_id' => FosterChild::factory(),
            'academic_year' => fake()->randomElement(['2022/2023', '2023/2024', '2024/2025']),
            'semester' => fake()->randomElement(['odd', 'even']),
            'gpa' => fake()->randomFloat(2, 1, 4),
            'class_rank' => fake()->numberBetween(1, 40),
            'achievements' => fake()->optional()->sentence(),
            'caregiver_notes' => fake()->optional()->sentence(),
            'report_card_path' => fake()->optional()->filePath(),
        ];
    }
}
