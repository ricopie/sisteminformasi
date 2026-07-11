<?php

namespace Tests\Unit\ValueObjects\Child;

use Domain\Beneficiaries\ValueObjects\Child\Education;
use Domain\Beneficiaries\ValueObjects\Enum\EducationStatus;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use Shared\ValueObjects\Enum\EducationLevel;
use Tests\TestCase;

class EducationTest extends TestCase
{
    #[Test]
    public function it_can_create_education(): void
    {
        $education = new Education(
            level: EducationLevel::ELEMENTARY,
            status: EducationStatus::CURRENTLY_ENROLLED,
            schoolName: 'SD Negeri 1',
            grade: 5,
        );

        $this->assertInstanceOf(Education::class, $education);
        $this->assertEquals('elementary', $education->toArray()['level']->value);
    }

    #[Test]
    public function it_throws_on_empty_school_name(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Education(
            level: EducationLevel::ELEMENTARY,
            status: EducationStatus::CURRENTLY_ENROLLED,
            schoolName: '',
            grade: 5,
        );
    }

    #[Test]
    public function it_throws_on_invalid_grade(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Education(
            level: EducationLevel::ELEMENTARY,
            status: EducationStatus::CURRENTLY_ENROLLED,
            schoolName: 'SD Negeri 1',
            grade: 0,
        );
    }

    #[Test]
    public function it_throws_on_grade_above_12(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Education(
            level: EducationLevel::ELEMENTARY,
            status: EducationStatus::CURRENTLY_ENROLLED,
            schoolName: 'SD Negeri 1',
            grade: 13,
        );
    }

    #[Test]
    public function it_throws_on_invalid_nisn(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Education(
            level: EducationLevel::ELEMENTARY,
            status: EducationStatus::CURRENTLY_ENROLLED,
            schoolName: 'SD Negeri 1',
            grade: 5,
            nisn: '12345',
        );
    }

    #[Test]
    public function it_can_create_with_valid_nisn(): void
    {
        $education = new Education(
            level: EducationLevel::JUNIOR_HIGH,
            status: EducationStatus::ENROLLED,
            schoolName: 'SMP Negeri 2',
            grade: 8,
            nisn: '1234567890',
        );

        $this->assertEquals('1234567890', $education->toArray()['nisn']);
    }

    #[Test]
    public function it_can_convert_to_array(): void
    {
        $education = new Education(
            level: EducationLevel::SENIOR_HIGH,
            status: EducationStatus::CURRENTLY_ENROLLED,
            schoolName: 'SMA Negeri 3',
            grade: 11,
            major: 'IPA',
        );

        $array = $education->toArray();

        $this->assertEquals('senior_high', $array['level']->value);
        $this->assertEquals('currently_enrolled', $array['status']->value);
        $this->assertEquals('SMA Negeri 3', $array['schoolName']);
        $this->assertEquals(11, $array['grade']);
        $this->assertEquals('IPA', $array['major']);
    }

    #[Test]
    public function it_can_create_from_array(): void
    {
        $data = [
            'level' => 'elementary',
            'status' => 'currently_enrolled',
            'schoolName' => 'SD Negeri 1',
            'grade' => 5,
        ];

        $education = Education::fromArray($data);

        $this->assertInstanceOf(Education::class, $education);
        $this->assertEquals(EducationLevel::ELEMENTARY, $education->toArray()['level']);
    }
}
