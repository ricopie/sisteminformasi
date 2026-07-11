<?php

namespace Tests\Unit\ValueObjects\Enum;

use Domain\Beneficiaries\ValueObjects\Enum\EducationStatus;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EducationStatusTest extends TestCase
{
    #[Test]
    public function it_has_currently_enrolled_case_with_correct_value(): void
    {
        $this->assertSame('currently_enrolled', EducationStatus::CURRENTLY_ENROLLED->value);
    }

    #[Test]
    public function it_has_enrolled_case_with_correct_value(): void
    {
        $this->assertSame('enrolled', EducationStatus::ENROLLED->value);
    }

    #[Test]
    public function it_has_not_enrolled_case_with_correct_value(): void
    {
        $this->assertSame('not_enrolled', EducationStatus::NOT_ENROLLED->value);
    }

    #[Test]
    public function it_has_graduated_case_with_correct_value(): void
    {
        $this->assertSame('graduated', EducationStatus::GRADUATED->value);
    }

    #[Test]
    public function it_has_transferred_case_with_correct_value(): void
    {
        $this->assertSame('transferred', EducationStatus::TRANSFERRED->value);
    }

    #[Test]
    public function it_has_dropped_out_case_with_correct_value(): void
    {
        $this->assertSame('dropped_out', EducationStatus::DROPPED_OUT->value);
    }

    #[Test]
    public function it_has_all_expected_cases(): void
    {
        $this->assertTrue(EducationStatus::tryFrom('currently_enrolled') === EducationStatus::CURRENTLY_ENROLLED);
        $this->assertTrue(EducationStatus::tryFrom('enrolled') === EducationStatus::ENROLLED);
        $this->assertTrue(EducationStatus::tryFrom('not_enrolled') === EducationStatus::NOT_ENROLLED);
        $this->assertTrue(EducationStatus::tryFrom('graduated') === EducationStatus::GRADUATED);
        $this->assertTrue(EducationStatus::tryFrom('transferred') === EducationStatus::TRANSFERRED);
        $this->assertTrue(EducationStatus::tryFrom('dropped_out') === EducationStatus::DROPPED_OUT);
    }

    #[Test]
    public function it_can_access_all_cases(): void
    {
        $this->assertInstanceOf(EducationStatus::class, EducationStatus::CURRENTLY_ENROLLED);
        $this->assertInstanceOf(EducationStatus::class, EducationStatus::ENROLLED);
        $this->assertInstanceOf(EducationStatus::class, EducationStatus::NOT_ENROLLED);
        $this->assertInstanceOf(EducationStatus::class, EducationStatus::GRADUATED);
        $this->assertInstanceOf(EducationStatus::class, EducationStatus::TRANSFERRED);
        $this->assertInstanceOf(EducationStatus::class, EducationStatus::DROPPED_OUT);
    }
}
