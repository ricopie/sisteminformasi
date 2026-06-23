<?php

namespace Database\Seeders;

use App\Models\FamilyCard;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        FamilyCard::factory()
            ->count(5)
            ->has(
                \App\Models\FosterChild::factory()
                    ->count(3)
                    ->has(\App\Models\ChildEducation::factory(), 'childEducation')
                    ->has(\App\Models\EducationHistory::factory()->count(2), 'educationHistories')
                    ->has(\App\Models\AcademicRecord::factory()->count(4), 'academicRecords')
                    ->has(\App\Models\EducationFunding::factory(), 'educationFundings'),
                'familyMember'
            )
            ->create();
    }
}
