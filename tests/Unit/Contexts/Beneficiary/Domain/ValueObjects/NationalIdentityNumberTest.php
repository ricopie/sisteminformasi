<?php

declare(strict_types=1);

namespace Tests\Unit\Contexts\Beneficiary\Domain\ValueObjects;

use Copie\Contexts\Beneficiary\Domain\ValueObjects\NationalIdentityNumber;
use Copie\Shared\Domain\Validator\ValidationException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class NationalIdentityNumberTest extends TestCase
{
    #[Test]
    public function valid_16_digit_nik_is_accepted(): void
    {
        $nik = '1234567890123456';
        $nationalIdentityNumber = new NationalIdentityNumber($nik);
        $this->assertSame($nik, $nationalIdentityNumber->value);
    }

    #[Test]
    public function blank_nik_throws_validation_exception(): void
    {
        $this->expectException(ValidationException::class);
        new NationalIdentityNumber('');
    }

    #[Test]
    public function short_nik_15_digits_throws_validation_exception(): void
    {
        $this->expectException(ValidationException::class);
        new NationalIdentityNumber('123456789012345');
    }

    #[Test]
    public function long_nik_17_digits_throws_validation_exception(): void
    {
        $this->expectException(ValidationException::class);
        new NationalIdentityNumber('12345678901234567');
    }

    #[Test]
    public function non_numeric_nik_throws_validation_exception(): void
    {
        $this->expectException(ValidationException::class);
        new NationalIdentityNumber('123456789012345A');
    }

    #[Test]
    public function from_array_creates_nik_from_array(): void
    {
        $data = ['value' => '1234567890123456'];
        $nationalIdentityNumber = NationalIdentityNumber::fromArray($data);
        $this->assertSame($data['value'], $nationalIdentityNumber->value);
    }

    #[Test]
    public function to_array_returns_correct_array(): void
    {
        $nationalIdentityNumber = new NationalIdentityNumber('1234567890123456');
        $expected = ['value' => '1234567890123456'];
        $this->assertSame($expected, $nationalIdentityNumber->toArray());
    }

    #[Test]
    public function equals_returns_true_for_same_nik(): void
    {
        $nik1 = new NationalIdentityNumber('1234567890123456');
        $nik2 = new NationalIdentityNumber('1234567890123456');
        $this->assertTrue($nik1->equals($nik2));
    }

    #[Test]
    public function equals_returns_false_for_different_nik(): void
    {
        $nik1 = new NationalIdentityNumber('1234567890123456');
        $nik2 = new NationalIdentityNumber('1234567890123457');
        $this->assertFalse($nik1->equals($nik2));
    }
}
