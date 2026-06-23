<?php

namespace Tests\Unit\Models;

use App\Models\AcademicRecord;
use App\Models\ChildEducation;
use App\Models\EducationFunding;
use App\Models\EducationHistory;
use App\Models\FamilyCard;
use App\Models\FosterChild;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FosterChildTest extends TestCase
{
    use RefreshDatabase;

    public function test_belongs_to_family_card(): void
    {
        $fosterChild = FosterChild::factory()->create();

        $this->assertInstanceOf(FamilyCard::class, $fosterChild->familyCard);
    }

    public function test_has_many_child_education(): void
    {
        $fosterChild = FosterChild::factory()
            ->has(ChildEducation::factory(), 'childEducation')
            ->create();

        $this->assertInstanceOf(ChildEducation::class, $fosterChild->childEducation->first());
    }

    public function test_has_many_education_histories(): void
    {
        $fosterChild = FosterChild::factory()
            ->has(EducationHistory::factory(), 'educationHistories')
            ->create();

        $this->assertInstanceOf(EducationHistory::class, $fosterChild->educationHistories->first());
    }

    public function test_has_many_academic_records(): void
    {
        $fosterChild = FosterChild::factory()
            ->has(AcademicRecord::factory(), 'academicRecords')
            ->create();

        $this->assertInstanceOf(AcademicRecord::class, $fosterChild->academicRecords->first());
    }

    public function test_has_many_education_fundings(): void
    {
        $fosterChild = FosterChild::factory()
            ->has(EducationFunding::factory(), 'educationFundings')
            ->create();

        $this->assertInstanceOf(EducationFunding::class, $fosterChild->educationFundings->first());
    }
}
