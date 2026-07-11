<?php

namespace Tests\Unit\DTOs\Child;

use Application\Beneficiaries\DTOs\Child\ChildAttributesData;
use Application\Beneficiaries\DTOs\Child\EducationData;
use Domain\Beneficiaries\ValueObjects\Enum\EducationStatus;
use PHPUnit\Framework\Attributes\Test;
use Shared\ValueObjects\Enum\EducationLevel;
use Tests\TestCase;

class ChildAttributesDataTest extends TestCase
{
    #[Test]
    public function it_can_create_via_constructor_with_all_fields(): void
    {
        $education = new EducationData(
            level: EducationLevel::SENIOR_HIGH,
            status: EducationStatus::CURRENTLY_ENROLLED,
            schoolName: 'SMA Negeri 1',
            grade: 12,
            major: 'Mathematics',
            nisn: '123456789'
        );

        $educationHistory = [
            new EducationData(
                level: EducationLevel::JUNIOR_HIGH,
                status: EducationStatus::GRADUATED,
                schoolName: 'SMP Negeri 1',
                grade: 9,
                major: 'Science',
                nisn: '987654321'
            ),
            new EducationData(
                level: EducationLevel::ELEMENTARY,
                status: EducationStatus::GRADUATED,
                schoolName: 'SDN 1',
                grade: 6
            ),
        ];

        $childAttributes = new ChildAttributesData(
            education: $education,
            educationHistory: $educationHistory,
            hobbies: ['reading', 'swimming', 'coding']
        );

        $this->assertInstanceOf(ChildAttributesData::class, $childAttributes);
        $this->assertInstanceOf(EducationData::class, $childAttributes->education);
        $this->assertCount(2, $childAttributes->educationHistory);
        $this->assertIsArray($childAttributes->hobbies);
        $this->assertSame('SMA Negeri 1', $childAttributes->education->schoolName);
        $this->assertSame('reading', $childAttributes->hobbies[0]);
        $this->assertSame('swimming', $childAttributes->hobbies[1]);
        $this->assertSame('coding', $childAttributes->hobbies[2]);
    }
}
