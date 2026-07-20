<?php

declare(strict_types=1);

namespace Tests\Unit\Shared\Domain\ValueObjects;

use Copie\Shared\Domain\Validator\ValidationException;
use Copie\Shared\Domain\ValueObjects\Address;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class AddressTest extends TestCase
{
    #[Test]
    public function valid_address_is_created_successfully(): void
    {
        $data = [
            'street' => 'Jl. Merdeka',
            'rt' => '01',
            'rw' => '02',
            'village' => 'Kampung A',
            'district' => 'Kec. A',
            'city' => 'Kota A',
            'province' => 'Provinsi A',
            'postalCode' => '12345',
        ];
        $address = Address::fromArray($data);
        $this->assertSame($data['street'], $address->street);
        $this->assertSame($data['rt'], $address->rt);
        $this->assertSame($data['rw'], $address->rw);
        $this->assertSame($data['village'], $address->village);
        $this->assertSame($data['district'], $address->district);
        $this->assertSame($data['city'], $address->city);
        $this->assertSame($data['province'], $address->province);
        $this->assertSame($data['postalCode'], $address->postalCode);
    }

    #[Test]
    public function full_address_format_is_correct(): void
    {
        $address = new Address(
            street: 'Jl. Merdeka',
            rt: '01',
            rw: '02',
            village: 'Kampung A',
            district: 'Kec. A',
            city: 'Kota A',
            province: 'Provinsi A',
            postalCode: '12345'
        );
        $expected = 'Jl. Merdeka, RT 01/RW 02, Kel. Kampung A, Kec. Kec. A, Kota A, Provinsi A 12345';
        $this->assertSame($expected, $address->fullAddress());
    }

    #[Test]
    public function blank_street_throws_validation_exception(): void
    {
        $this->expectException(ValidationException::class);
        new Address('', '01', '02', 'Village', 'District', 'City', 'Province', '12345');
    }

    #[Test]
    public function blank_rt_throws_validation_exception(): void
    {
        $this->expectException(ValidationException::class);
        new Address('Street', '', '02', 'Village', 'District', 'City', 'Province', '12345');
    }

    #[Test]
    public function invalid_rt_non_digits_throws_validation_exception(): void
    {
        $this->expectException(ValidationException::class);
        new Address('Street', '01A', '02', 'Village', 'District', 'City', 'Province', '12345');
    }

    #[Test]
    public function short_rt_one_digit_throws_validation_exception(): void
    {
        $this->expectException(ValidationException::class);
        new Address('Street', '1', '02', 'Village', 'District', 'City', 'Province', '12345');
    }

    #[Test]
    public function long_rt_four_digits_throws_validation_exception(): void
    {
        $this->expectException(ValidationException::class);
        new Address('Street', '0123', '02', 'Village', 'District', 'City', 'Province', '12345');
    }

    #[Test]
    public function invalid_postal_code_non_digits_throws_validation_exception(): void
    {
        $this->expectException(ValidationException::class);
        new Address('Street', '01', '02', 'Village', 'District', 'City', 'Province', '1234A');
    }

    #[Test]
    public function wrong_postal_code_length_throws_validation_exception(): void
    {
        $this->expectException(ValidationException::class);
        new Address('Street', '01', '02', 'Village', 'District', 'City', 'Province', '1234');
    }

    #[Test]
    public function from_array_creates_address_from_array(): void
    {
        $data = [
            'street' => 'Jl. Merdeka',
            'rt' => '01',
            'rw' => '02',
            'village' => 'Kampung A',
            'district' => 'Kec. A',
            'city' => 'Kota A',
            'province' => 'Provinsi A',
            'postalCode' => '12345',
        ];
        $address = Address::fromArray($data);
        $this->assertSame($data['street'], $address->street);
        $this->assertSame($data['rt'], $address->rt);
        $this->assertSame($data['rw'], $address->rw);
        $this->assertSame($data['village'], $address->village);
        $this->assertSame($data['district'], $address->district);
        $this->assertSame($data['city'], $address->city);
        $this->assertSame($data['province'], $address->province);
        $this->assertSame($data['postalCode'], $address->postalCode);
    }

    #[Test]
    public function to_array_returns_correct_array(): void
    {
        $address = new Address(
            street: 'Jl. Merdeka',
            rt: '01',
            rw: '02',
            village: 'Kampung A',
            district: 'Kec. A',
            city: 'Kota A',
            province: 'Provinsi A',
            postalCode: '12345'
        );
        $expected = [
            'street' => 'Jl. Merdeka',
            'rt' => '01',
            'rw' => '02',
            'village' => 'Kampung A',
            'district' => 'Kec. A',
            'city' => 'Kota A',
            'province' => 'Provinsi A',
            'postalCode' => '12345',
        ];
        $this->assertSame($expected, $address->toArray());
    }

    #[Test]
    public function equals_returns_true_for_same_values(): void
    {
        $address1 = new Address(
            street: 'Jl. Merdeka',
            rt: '01',
            rw: '02',
            village: 'Kampung A',
            district: 'Kec. A',
            city: 'Kota A',
            province: 'Provinsi A',
            postalCode: '12345'
        );
        $address2 = new Address(
            street: 'Jl. Merdeka',
            rt: '01',
            rw: '02',
            village: 'Kampung A',
            district: 'Kec. A',
            city: 'Kota A',
            province: 'Provinsi A',
            postalCode: '12345'
        );
        $this->assertTrue($address1->equals($address2));
    }

    #[Test]
    public function equals_returns_false_for_different_values(): void
    {
        $address1 = new Address(
            street: 'Jl. Merdeka',
            rt: '01',
            rw: '02',
            village: 'Kampung A',
            district: 'Kec. A',
            city: 'Kota A',
            province: 'Provinsi A',
            postalCode: '12345'
        );
        $address2 = new Address(
            street: 'Jl. Merdeka',
            rt: '01',
            rw: '02',
            village: 'Kampung B',
            district: 'Kec. A',
            city: 'Kota A',
            province: 'Provinsi A',
            postalCode: '12345'
        );
        $this->assertFalse($address1->equals($address2));
    }

    #[Test]
    public function __to_string_returns_full_address(): void
    {
        $address = new Address(
            street: 'Jl. Merdeka',
            rt: '01',
            rw: '02',
            village: 'Kampung A',
            district: 'Kec. A',
            city: 'Kota A',
            province: 'Provinsi A',
            postalCode: '12345'
        );
        $expected = 'Jl. Merdeka, RT 01/RW 02, Kel. Kampung A, Kec. Kec. A, Kota A, Provinsi A 12345';
        $this->assertSame($expected, (string) $address);
    }
}
