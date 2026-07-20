<?php

declare(strict_types=1);

namespace Tests\Unit\Contexts\Beneficiary\Domain\ValueObjects;

use Copie\Contexts\Beneficiary\Domain\Enums\EducationStatus;
use Copie\Contexts\Beneficiary\Domain\ValueObjects\Education;
use Copie\Shared\Domain\Enums\EducationLevel;
use Copie\Shared\Domain\Validator\ValidationException;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class EducationTest extends TestCase
{
    #[Test]
    public function valid_education_with_all_required_fields(): void
    {
        $education = new Education(
            level: EducationLevel::SENIOR_HIGH,
            status: EducationStatus::GRADUATED,
            schoolName: 'SMA Negeri 1',
            grade: 12,
            major: 'IPA',
            nisn: '1234567890'
        );
        $this->assertSame('SMA Negeri 1', $education->schoolName);
        $this->assertSame(12, $education->grade);
        $this->assertSame('IPA', $education->major);
        $this->assertSame('1234567890', $education->nisn);
    }

    #[Test]
    public function blank_school_name_throws_validation_exception(): void
    {
        $this->expectException(ValidationException::class);
        new Education(
            level: EducationLevel::SENIOR_HIGH,
            status: EducationStatus::GRADUATED,
            schoolName: '',
            grade: 12,
            major: 'IPA',
            nisn: '1234567890'
        );
    }

    #[Test]
    public function grade_0_throws_invalid_argument_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Education(
            level: EducationLevel::SENIOR_HIGH,
            status: EducationStatus::GRADUATED,
            schoolName: 'SMA Negeri 1',
            grade: 0,
            major: 'IPA',
            nisn: '1234567890'
        );
    }

    #[Test]
    public function grade_13_throws_invalid_argument_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Education(
            level: EducationLevel::SENIOR_HIGH,
            status: EducationStatus::GRADUATED,
            schoolName: 'SMA Negeri 1',
            grade: 13,
            major: 'IPA',
            nisn: '1234567890'
        );
    }

    #[Test]
    public function grade_1_is_valid(): void
    {
        $education = new Education(
            level: EducationLevel::ELEMENTARY,
            status: EducationStatus::CURRENTLY_ENROLLED,
            schoolName: 'SD Negeri 1',
            grade: 1
        );
        $this->assertSame(1, $education->grade);
    }

    #[Test]
    public function grade_12_is_valid(): void
    {
        $education = new Education(
            level: EducationLevel::SENIOR_HIGH,
            status: EducationStatus::GRADUATED,
            schoolName: 'SMA Negeri 1',
            grade: 12,
            major: 'IPA',
            nisn: '1234567890'
        );
        $this->assertSame(12, $education->grade);
    }

    #[Test]
    public function valid_nisn_10_digits_is_accepted(): void
    {
        $education = new Education(
            level: EducationLevel::JUNIOR_HIGH,
            status: EducationStatus::GRADUATED,
            schoolName: 'SMP Negeri 1',
            grade: 9,
            nisn: '1234567890'
        );
        $this->assertSame('1234567890', $education->nisn);
    }

    #[Test]
    public function invalid_nisn_9_digits_throws_invalid_argument_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Education(
            level: EducationLevel::JUNIOR_HIGH,
            status: EducationStatus::GRADUATED,
            schoolName: 'SMP Negeri 1',
            grade: 9,
            nisn: '123456789'
        );
    }

    #[Test]
    public function invalid_nisn_11_digits_throws_invalid_argument_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Education(
            level: EducationLevel::JUNIOR_HIGH,
            status: EducationStatus::GRADUATED,
            schoolName: 'SMP Negeri 1',
            grade: 9,
            nisn: '12345678901'
        );
    }

    #[Test]
    public function non_numeric_nisn_throws_invalid_argument_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Education(
            level: EducationLevel::JUNIOR_HIGH,
            status: EducationStatus::GRADUATED,
            schoolName: 'SMP Negeri 1',
            grade: 9,
            nisn: '12345678A9'
        );
    }

    #[Test]
    public function null_nisn_is_allowed(): void
    {
        $education = new Education(
            level: EducationLevel::JUNIOR_HIGH,
            status: EducationStatus::GRADUATED,
            schoolName: 'SMP Negeri 1',
            grade: 9
        );
        $this->assertNull($education->nisn);
    }

    #[Test]
    public function null_major_is_allowed(): void
    {
        $education = new Education(
            level: EducationLevel::JUNIOR_HIGH,
            status: EducationStatus::GRADUATED,
            schoolName: 'SMP Negeri 1',
            grade: 9
        );
        $this->assertNull($education->major);
    }

    #[Test]
    public function from_array_creates_education_from_array(): void
    {
        $data = [
            'level' => 'senior_high',
            'status' => 'graduated',
            'schoolName' => 'SMA Negeri 1',
            'grade' => 12,
            'major' => 'IPA',
            'nisn' => '1234567890',
        ];
        $education = Education::fromArray($data);
        $this->assertSame('SMA Negeri 1', $education->schoolName);
        $this->assertSame(12, $education->grade);
        $this->assertSame('IPA', $education->major);
        $this->assertSame('1234567890', $education->nisn);
    }

    #[Test]
    public function to_array_returns_correct_array(): void
    {
        $education = new Education(
            level: EducationLevel::SENIOR_HIGH,
            status: EducationStatus::GRADUATED,
            schoolName: 'SMA Negeri 1',
            grade: 12,
            major: 'IPA',
            nisn: '1234567890'
        );
        $expected = [
            'level' => 'senior_high',
            'status' => 'graduated',
            'schoolName' => 'SMA Negeri 1',
            'grade' => 12,
            'major' => 'IPA',
            'nisn' => '1234567890',
        ];
        $this->assertSame($expected, $education->toArray());
    }

    #[Test]
    public function equals_returns_true_for_same_education(): void
    {
        $education1 = new Education(
            level: EducationLevel::SENIOR_HIGH,
            status: EducationStatus::GRADUATED,
            schoolName: 'SMA Negeri 1',
            grade: 12,
            major: 'IPA',
            nisn: '1234567890'
        );
        $education2 = new Education(
            level: EducationLevel::SENIOR_HIGH,
            status: EducationStatus::GRADUATED,
            schoolName: 'SMA Negeri 1',
            grade: 12,
            major: 'IPA',
            nisn: '1234567890'
        );
        $this->assertTrue($education1->equals($education2));
    }

    #[Test]
    public function equals_returns_false_for_different_education(): void
    {
        $education1 = new Education(
            level: EducationLevel::SENIOR_HIGH,
            status: EducationStatus::GRADUATED,
            schoolName: 'SMA Negeri 1',
            grade: 12,
            major: 'IPA',
            nisn: '1234567890'
        );
        $education2 = new Education(
            level: EducationLevel::JUNIOR_HIGH,
            status: EducationStatus::GRADUATED,
            schoolName: 'SMP Negeri 1',
            grade: 9,
            major: 'IPS',
            nisn: '1234567890'
        );
        $this->assertFalse($education1->equals($education2));
    }
}
