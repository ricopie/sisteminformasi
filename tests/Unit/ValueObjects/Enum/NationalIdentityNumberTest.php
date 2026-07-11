<?php

namespace Tests\Unit\ValueObjects\Enum;

use Domain\Beneficiaries\ValueObjects\Enum\NationalIdentityNumber;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class NationalIdentityNumberTest extends TestCase
{
    #[Test]
    public function it_can_create_nik_with_valid_16_digit_number(): void
    {
        $nik = new NationalIdentityNumber('1234567890123456');
        $this->assertInstanceOf(NationalIdentityNumber::class, $nik);
        $this->assertEquals('1234567890123456', $nik->value);
    }

    #[Test]
    public function it_throws_invalid_argument_exception_when_less_than_16_digits(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new NationalIdentityNumber('123456789012345');
    }

    #[Test]
    public function it_throws_invalid_argument_exception_when_more_than_16_digits(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new NationalIdentityNumber('12345678901234567');
    }

    #[Test]
    public function it_throws_invalid_argument_exception_when_contains_non_numeric(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new NationalIdentityNumber('123456789012345A');
    }

    #[Test]
    public function it_can_compare_equal_niks(): void
    {
        $nik1 = new NationalIdentityNumber('1234567890123456');
        $nik2 = new NationalIdentityNumber('1234567890123456');
        $this->assertEquals($nik1->value, $nik2->value);
    }

    #[Test]
    public function it_can_compare_different_niks(): void
    {
        $nik1 = new NationalIdentityNumber('1234567890123456');
        $nik2 = new NationalIdentityNumber('6543210987654321');
        $this->assertNotEquals($nik1->value, $nik2->value);
    }
}
