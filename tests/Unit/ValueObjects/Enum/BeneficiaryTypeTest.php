<?php

namespace Tests\Unit\ValueObjects\Enum;

use Domain\Beneficiaries\ValueObjects\Enum\BeneficiaryType;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BeneficiaryTypeTest extends TestCase
{
    #[Test]
    public function it_has_child_case_with_correct_value(): void
    {
        $this->assertSame('child', BeneficiaryType::CHILD->value);
    }

    #[Test]
    public function it_has_elderly_case_with_correct_value(): void
    {
        $this->assertSame('elderly', BeneficiaryType::ELDERLY->value);
    }

    #[Test]
    public function it_has_disabled_case_with_correct_value(): void
    {
        $this->assertSame('disabled', BeneficiaryType::DISABLED->value);
    }

    #[Test]
    public function it_has_general_case_with_correct_value(): void
    {
        $this->assertSame('general', BeneficiaryType::GENERAL->value);
    }

    #[Test]
    public function it_has_all_expected_cases(): void
    {
        $this->assertTrue(BeneficiaryType::tryFrom('child') === BeneficiaryType::CHILD);
        $this->assertTrue(BeneficiaryType::tryFrom('elderly') === BeneficiaryType::ELDERLY);
        $this->assertTrue(BeneficiaryType::tryFrom('disabled') === BeneficiaryType::DISABLED);
        $this->assertTrue(BeneficiaryType::tryFrom('general') === BeneficiaryType::GENERAL);
    }

    #[Test]
    public function it_can_access_all_cases(): void
    {
        $this->assertInstanceOf(BeneficiaryType::class, BeneficiaryType::CHILD);
        $this->assertInstanceOf(BeneficiaryType::class, BeneficiaryType::ELDERLY);
        $this->assertInstanceOf(BeneficiaryType::class, BeneficiaryType::DISABLED);
        $this->assertInstanceOf(BeneficiaryType::class, BeneficiaryType::GENERAL);
    }
}
