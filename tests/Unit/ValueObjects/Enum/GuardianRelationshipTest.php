<?php

namespace Tests\Unit\ValueObjects\Enum;

use Domain\Beneficiaries\ValueObjects\Enum\GuardianRelationship;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GuardianRelationshipTest extends TestCase
{
    #[Test]
    public function it_has_father_case_with_correct_value(): void
    {
        $this->assertSame('father', GuardianRelationship::FATHER->value);
    }

    #[Test]
    public function it_has_mother_case_with_correct_value(): void
    {
        $this->assertSame('mother', GuardianRelationship::MOTHER->value);
    }

    #[Test]
    public function it_has_grandfather_case_with_correct_value(): void
    {
        $this->assertSame('grandfather', GuardianRelationship::GRANDFATHER->value);
    }

    #[Test]
    public function it_has_grandmother_case_with_correct_value(): void
    {
        $this->assertSame('grandmother', GuardianRelationship::GRANDMOTHER->value);
    }

    #[Test]
    public function it_has_uncle_case_with_correct_value(): void
    {
        $this->assertSame('uncle', GuardianRelationship::UNCLE->value);
    }

    #[Test]
    public function it_has_aunt_case_with_correct_value(): void
    {
        $this->assertSame('aunt', GuardianRelationship::AUNT->value);
    }

    #[Test]
    public function it_has_sibling_case_with_correct_value(): void
    {
        $this->assertSame('sibling', GuardianRelationship::SIBLING->value);
    }

    #[Test]
    public function it_has_foster_parent_case_with_correct_value(): void
    {
        $this->assertSame('foster_parent', GuardianRelationship::FOSTER_PARENT->value);
    }

    #[Test]
    public function it_has_legal_guardian_case_with_correct_value(): void
    {
        $this->assertSame('legal_guardian', GuardianRelationship::LEGAL_GUARDIAN->value);
    }

    #[Test]
    public function it_has_other_case_with_correct_value(): void
    {
        $this->assertSame('other', GuardianRelationship::OTHER->value);
    }

    #[Test]
    public function it_has_all_expected_cases(): void
    {
        $this->assertTrue(GuardianRelationship::tryFrom('father') === GuardianRelationship::FATHER);
        $this->assertTrue(GuardianRelationship::tryFrom('mother') === GuardianRelationship::MOTHER);
        $this->assertTrue(GuardianRelationship::tryFrom('grandfather') === GuardianRelationship::GRANDFATHER);
        $this->assertTrue(GuardianRelationship::tryFrom('grandmother') === GuardianRelationship::GRANDMOTHER);
        $this->assertTrue(GuardianRelationship::tryFrom('uncle') === GuardianRelationship::UNCLE);
        $this->assertTrue(GuardianRelationship::tryFrom('aunt') === GuardianRelationship::AUNT);
        $this->assertTrue(GuardianRelationship::tryFrom('sibling') === GuardianRelationship::SIBLING);
        $this->assertTrue(GuardianRelationship::tryFrom('foster_parent') === GuardianRelationship::FOSTER_PARENT);
        $this->assertTrue(GuardianRelationship::tryFrom('legal_guardian') === GuardianRelationship::LEGAL_GUARDIAN);
        $this->assertTrue(GuardianRelationship::tryFrom('other') === GuardianRelationship::OTHER);
    }

    #[Test]
    public function it_can_access_all_cases(): void
    {
        $this->assertInstanceOf(GuardianRelationship::class, GuardianRelationship::FATHER);
        $this->assertInstanceOf(GuardianRelationship::class, GuardianRelationship::MOTHER);
        $this->assertInstanceOf(GuardianRelationship::class, GuardianRelationship::GRANDFATHER);
        $this->assertInstanceOf(GuardianRelationship::class, GuardianRelationship::GRANDMOTHER);
        $this->assertInstanceOf(GuardianRelationship::class, GuardianRelationship::UNCLE);
        $this->assertInstanceOf(GuardianRelationship::class, GuardianRelationship::AUNT);
        $this->assertInstanceOf(GuardianRelationship::class, GuardianRelationship::SIBLING);
        $this->assertInstanceOf(GuardianRelationship::class, GuardianRelationship::FOSTER_PARENT);
        $this->assertInstanceOf(GuardianRelationship::class, GuardianRelationship::LEGAL_GUARDIAN);
        $this->assertInstanceOf(GuardianRelationship::class, GuardianRelationship::OTHER);
    }
}
