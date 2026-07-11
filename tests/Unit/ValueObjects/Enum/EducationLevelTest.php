<?php

namespace Tests\Unit\ValueObjects\Enum;

use PHPUnit\Framework\Attributes\Test;
use Shared\ValueObjects\Enum\EducationLevel;
use Tests\TestCase;

class EducationLevelTest extends TestCase
{
    #[Test]
    public function it_has_none_case_with_correct_value(): void
    {
        $this->assertSame('none', EducationLevel::NONE->value);
    }

    #[Test]
    public function it_has_elementary_case_with_correct_value(): void
    {
        $this->assertSame('elementary', EducationLevel::ELEMENTARY->value);
    }

    #[Test]
    public function it_has_junior_high_case_with_correct_value(): void
    {
        $this->assertSame('junior_high', EducationLevel::JUNIOR_HIGH->value);
    }

    #[Test]
    public function it_has_senior_high_case_with_correct_value(): void
    {
        $this->assertSame('senior_high', EducationLevel::SENIOR_HIGH->value);
    }

    #[Test]
    public function it_has_diploma_case_with_correct_value(): void
    {
        $this->assertSame('diploma', EducationLevel::DIPLOMA->value);
    }

    #[Test]
    public function it_has_bachelor_case_with_correct_value(): void
    {
        $this->assertSame('bachelor', EducationLevel::BACHELOR->value);
    }

    #[Test]
    public function it_has_master_case_with_correct_value(): void
    {
        $this->assertSame('master', EducationLevel::MASTER->value);
    }

    #[Test]
    public function it_has_doctor_case_with_correct_value(): void
    {
        $this->assertSame('doctor', EducationLevel::DOCTOR->value);
    }

    #[Test]
    public function it_has_all_expected_cases(): void
    {
        $this->assertTrue(EducationLevel::tryFrom('none') === EducationLevel::NONE);
        $this->assertTrue(EducationLevel::tryFrom('elementary') === EducationLevel::ELEMENTARY);
        $this->assertTrue(EducationLevel::tryFrom('junior_high') === EducationLevel::JUNIOR_HIGH);
        $this->assertTrue(EducationLevel::tryFrom('senior_high') === EducationLevel::SENIOR_HIGH);
        $this->assertTrue(EducationLevel::tryFrom('diploma') === EducationLevel::DIPLOMA);
        $this->assertTrue(EducationLevel::tryFrom('bachelor') === EducationLevel::BACHELOR);
        $this->assertTrue(EducationLevel::tryFrom('master') === EducationLevel::MASTER);
        $this->assertTrue(EducationLevel::tryFrom('doctor') === EducationLevel::DOCTOR);
    }

    #[Test]
    public function it_can_access_all_cases(): void
    {
        $this->assertInstanceOf(EducationLevel::class, EducationLevel::NONE);
        $this->assertInstanceOf(EducationLevel::class, EducationLevel::ELEMENTARY);
        $this->assertInstanceOf(EducationLevel::class, EducationLevel::JUNIOR_HIGH);
        $this->assertInstanceOf(EducationLevel::class, EducationLevel::SENIOR_HIGH);
        $this->assertInstanceOf(EducationLevel::class, EducationLevel::DIPLOMA);
        $this->assertInstanceOf(EducationLevel::class, EducationLevel::BACHELOR);
        $this->assertInstanceOf(EducationLevel::class, EducationLevel::MASTER);
        $this->assertInstanceOf(EducationLevel::class, EducationLevel::DOCTOR);
    }
}
