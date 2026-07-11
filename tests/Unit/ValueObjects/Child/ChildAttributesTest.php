<?php

namespace Tests\Unit\ValueObjects\Child;

use Domain\Beneficiaries\ValueObjects\Child\ChildAttributes;
use Domain\Beneficiaries\ValueObjects\Child\Education;
use Domain\Beneficiaries\ValueObjects\Enum\EducationStatus;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use Shared\ValueObjects\Enum\EducationLevel;
use Tests\TestCase;

class ChildAttributesTest extends TestCase
{
    #[Test]
    public function it_can_create_child_attributes(): void
    {
        $education = new Education(
            level: EducationLevel::ELEMENTARY,
            status: EducationStatus::CURRENTLY_ENROLLED,
            schoolName: 'SD Negeri 1',
            grade: 5,
        );

        $attributes = new ChildAttributes($education);

        $this->assertInstanceOf(ChildAttributes::class, $attributes);
    }

    #[Test]
    public function it_can_create_with_hobbies(): void
    {
        $education = new Education(
            level: EducationLevel::ELEMENTARY,
            status: EducationStatus::CURRENTLY_ENROLLED,
            schoolName: 'SD Negeri 1',
            grade: 5,
        );

        $attributes = new ChildAttributes(
            education: $education,
            hobbies: ['reading', 'swimming'],
        );

        $this->assertEquals(['reading', 'swimming'], $attributes->toArray()['hobbies']);
    }

    #[Test]
    public function it_throws_on_empty_hobby(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $education = new Education(
            level: EducationLevel::ELEMENTARY,
            status: EducationStatus::CURRENTLY_ENROLLED,
            schoolName: 'SD Negeri 1',
            grade: 5,
        );

        new ChildAttributes(
            education: $education,
            hobbies: ['reading', ''],
        );
    }

    #[Test]
    public function it_can_create_with_education_history(): void
    {
        $current = new Education(
            level: EducationLevel::JUNIOR_HIGH,
            status: EducationStatus::CURRENTLY_ENROLLED,
            schoolName: 'SMP Negeri 2',
            grade: 8,
        );

        $previous = new Education(
            level: EducationLevel::ELEMENTARY,
            status: EducationStatus::GRADUATED,
            schoolName: 'SD Negeri 1',
            grade: 6,
        );

        $attributes = new ChildAttributes(
            education: $current,
            educationHistory: [$previous],
        );

        $this->assertCount(1, $attributes->toArray()['educationHistory']);
    }

    #[Test]
    public function it_can_create_from_array(): void
    {
        $data = [
            'education' => [
                'level' => 'senior_high',
                'status' => 'currently_enrolled',
                'schoolName' => 'SMA Negeri 3',
                'grade' => 11,
                'major' => 'IPA',
            ],
            'hobbies' => ['badminton', 'coding'],
        ];

        $attributes = ChildAttributes::fromArray($data);

        $this->assertInstanceOf(ChildAttributes::class, $attributes);
        $this->assertEquals(['badminton', 'coding'], $attributes->toArray()['hobbies']);
        $this->assertEquals('SMA Negeri 3', $attributes->toArray()['education']['schoolName']);
    }

    #[Test]
    public function it_throws_on_invalid_education_history_item(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $education = new Education(
            level: EducationLevel::ELEMENTARY,
            status: EducationStatus::CURRENTLY_ENROLLED,
            schoolName: 'SD Negeri 1',
            grade: 5,
        );

        new ChildAttributes(
            education: $education,
            educationHistory: [123],
        );
    }
}
