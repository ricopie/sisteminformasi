<?php

declare(strict_types=1);

namespace Tests\Unit\Contexts\Beneficiary\Domain\ValueObjects;

use Copie\Contexts\Beneficiary\Domain\Enums\EducationStatus;
use Copie\Contexts\Beneficiary\Domain\ValueObjects\ChildAttributes;
use Copie\Contexts\Beneficiary\Domain\ValueObjects\Education;
use Copie\Contexts\Beneficiary\Domain\ValueObjects\SpecificAttributes;
use Copie\Shared\Domain\Enums\EducationLevel;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ChildAttributesTest extends TestCase
{
    #[Test]
    public function valid_child_attributes_with_education_history_and_hobbies(): void
    {
        $education = new Education(
            level: EducationLevel::SENIOR_HIGH,
            status: EducationStatus::GRADUATED,
            schoolName: 'SMA Negeri 1',
            grade: 12,
            major: 'IPA',
            nisn: '1234567890'
        );
        $educationHistory = [
            new Education(
                level: EducationLevel::JUNIOR_HIGH,
                status: EducationStatus::GRADUATED,
                schoolName: 'SMP Negeri 1',
                grade: 9,
                nisn: '0987654321'
            ),
        ];
        $hobbies = ['Reading', 'Football', 'Music'];

        $childAttributes = new ChildAttributes(
            education: $education,
            educationHistory: $educationHistory,
            hobbies: $hobbies
        );

        $this->assertSame($education, $childAttributes->education);
        $this->assertSame($educationHistory, $childAttributes->educationHistory);
        $this->assertSame($hobbies, $childAttributes->hobbies);
    }

    #[Test]
    public function valid_child_attributes_with_empty_history_and_hobbies(): void
    {
        $education = new Education(
            level: EducationLevel::ELEMENTARY,
            status: EducationStatus::CURRENTLY_ENROLLED,
            schoolName: 'SD Negeri 1',
            grade: 3
        );

        $childAttributes = new ChildAttributes(
            education: $education,
            educationHistory: [],
            hobbies: []
        );

        $this->assertSame($education, $childAttributes->education);
        $this->assertEmpty($childAttributes->educationHistory);
        $this->assertEmpty($childAttributes->hobbies);
    }

    #[Test]
    public function from_array_creates_child_attributes_from_array(): void
    {
        $data = [
            'education' => [
                'level' => 'senior_high',
                'status' => 'graduated',
                'schoolName' => 'SMA Negeri 1',
                'grade' => 12,
                'major' => 'IPA',
                'nisn' => '1234567890',
            ],
            'educationHistory' => [
                [
                    'level' => 'junior_high',
                    'status' => 'graduated',
                    'schoolName' => 'SMP Negeri 1',
                    'grade' => 9,
                    'major' => null,
                    'nisn' => '0987654321',
                ],
            ],
            'hobbies' => ['Reading', 'Football', 'Music'],
        ];

        $childAttributes = ChildAttributes::fromArray($data);
        $this->assertInstanceOf(Education::class, $childAttributes->education);
        $this->assertSame('SMA Negeri 1', $childAttributes->education->schoolName);
        $this->assertCount(1, $childAttributes->educationHistory);
        $this->assertSame('Reading', $childAttributes->hobbies[0]);
        $this->assertSame('Football', $childAttributes->hobbies[1]);
        $this->assertSame('Music', $childAttributes->hobbies[2]);
    }

    #[Test]
    public function to_array_returns_correct_array(): void
    {
        $education = new Education(
            level: EducationLevel::JUNIOR_HIGH,
            status: EducationStatus::GRADUATED,
            schoolName: 'SMP Negeri 1',
            grade: 9,
            nisn: '0987654321'
        );
        $educationHistory = [
            new Education(
                level: EducationLevel::ELEMENTARY,
                status: EducationStatus::GRADUATED,
                schoolName: 'SD Negeri 1',
                grade: 3
            ),
        ];
        $hobbies = ['Reading', 'Football', 'Music'];

        $childAttributes = new ChildAttributes(
            education: $education,
            educationHistory: $educationHistory,
            hobbies: $hobbies
        );

        $result = $childAttributes->toArray();
        $this->assertSame('SMP Negeri 1', $result['education']['schoolName']);
        $this->assertCount(1, $result['educationHistory']);
        $this->assertSame('SD Negeri 1', $result['educationHistory'][0]['schoolName']);
        $this->assertSame(['Reading', 'Football', 'Music'], $result['hobbies']);
    }

    #[Test]
    public function to_array_preserves_education_history_order(): void
    {
        $education1 = new Education(
            level: EducationLevel::ELEMENTARY,
            status: EducationStatus::CURRENTLY_ENROLLED,
            schoolName: 'SD Negeri 1',
            grade: 1
        );
        $education2 = new Education(
            level: EducationLevel::JUNIOR_HIGH,
            status: EducationStatus::CURRENTLY_ENROLLED,
            schoolName: 'SMP Negeri 1',
            grade: 7
        );
        $education3 = new Education(
            level: EducationLevel::SENIOR_HIGH,
            status: EducationStatus::CURRENTLY_ENROLLED,
            schoolName: 'SMA Negeri 1',
            grade: 10
        );

        $childAttributes = new ChildAttributes(
            education: $education1,
            educationHistory: [$education2, $education3],
            hobbies: []
        );

        $result = $childAttributes->toArray();
        $this->assertSame('SMP Negeri 1', $result['educationHistory'][0]['schoolName']);
        $this->assertSame('SMA Negeri 1', $result['educationHistory'][1]['schoolName']);
    }

    #[Test]
    public function to_array_preserves_hobbies_order(): void
    {
        $education = new Education(
            level: EducationLevel::ELEMENTARY,
            status: EducationStatus::CURRENTLY_ENROLLED,
            schoolName: 'SD Negeri 1',
            grade: 1
        );

        $hobbies = ['Chess', 'Swimming', 'Painting', 'Coding', 'Reading'];

        $childAttributes = new ChildAttributes(
            education: $education,
            educationHistory: [],
            hobbies: $hobbies
        );

        $result = $childAttributes->toArray();
        $this->assertSame(['Chess', 'Swimming', 'Painting', 'Coding', 'Reading'], $result['hobbies']);
    }

    #[Test]
    public function child_attributes_implements_specific_attributes_interface(): void
    {
        $education = new Education(
            level: EducationLevel::ELEMENTARY,
            status: EducationStatus::CURRENTLY_ENROLLED,
            schoolName: 'SD Negeri 1',
            grade: 1
        );

        $childAttributes = new ChildAttributes(
            education: $education,
            educationHistory: [],
            hobbies: []
        );

        $this->assertInstanceOf(SpecificAttributes::class, $childAttributes);
    }
}
