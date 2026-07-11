<?php

namespace Tests\Unit\ValueObjects\Enum;

use PHPUnit\Framework\Attributes\Test;
use Shared\ValueObjects\Enum\Gender;
use Tests\TestCase;

class GenderTest extends TestCase
{
    #[Test]
    public function it_has_male_case_with_correct_value(): void
    {
        $this->assertSame('male', Gender::MALE->value);
    }

    #[Test]
    public function it_has_female_case_with_correct_value(): void
    {
        $this->assertSame('female', Gender::FEMALE->value);
    }

    #[Test]
    public function it_has_correct_case_names(): void
    {
        $this->assertTrue(Gender::tryFrom('male') === Gender::MALE);
        $this->assertTrue(Gender::tryFrom('female') === Gender::FEMALE);
    }

    #[Test]
    public function it_can_access_male_case(): void
    {
        $this->assertInstanceOf(Gender::class, Gender::MALE);
        $this->assertEquals('male', Gender::MALE->value);
    }

    #[Test]
    public function it_can_access_female_case(): void
    {
        $this->assertInstanceOf(Gender::class, Gender::FEMALE);
        $this->assertEquals('female', Gender::FEMALE->value);
    }
}
