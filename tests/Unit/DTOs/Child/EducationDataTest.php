<?php

namespace Tests\Unit\DTOs\Child;

use Application\Beneficiaries\DTOs\Child\EducationData;
use Domain\Beneficiaries\ValueObjects\Enum\EducationStatus;
use PHPUnit\Framework\Attributes\Test;
use Shared\ValueObjects\Enum\EducationLevel;
use Tests\TestCase;

class EducationDataTest extends TestCase
{
    #[Test]
    public function it_can_create_via_constructor_with_all_fields(): void
    {
        $educationData = new EducationData(
            level: EducationLevel::SENIOR_HIGH,
            status: EducationStatus::CURRENTLY_ENROLLED,
            schoolName: 'SMA Negeri 1',
            grade: 12,
            major: 'Mathematics',
            nisn: '123456789'
        );

        $this->assertInstanceOf(EducationData::class, $educationData);
        $this->assertSame(EducationLevel::SENIOR_HIGH, $educationData->level);
        $this->assertSame(EducationStatus::CURRENTLY_ENROLLED, $educationData->status);
        $this->assertSame('SMA Negeri 1', $educationData->schoolName);
        $this->assertSame(12, $educationData->grade);
        $this->assertSame('Mathematics', $educationData->major);
        $this->assertSame('123456789', $educationData->nisn);
    }
}
