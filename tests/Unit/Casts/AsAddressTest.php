<?php

namespace Tests\Unit\Casts;

use App\Casts\AsAddress;
use App\ValueObjects\Address;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class AsAddressTest extends TestCase
{
    private AsAddress $cast;

    private Model $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cast = new AsAddress;
        $this->model = $this->createMock(Model::class);
    }

    public function test_get_returns_address_value_object(): void
    {
        $attributes = [
            'street' => 'Jl. Merdeka No. 1',
            'rt' => '001',
            'rw' => '002',
            'village' => 'Sukamaju',
            'sub_district' => 'Cimahi',
            'city' => 'Bandung',
            'province' => 'Jawa Barat',
            'postal_code' => '40123',
        ];
        $address = $this->cast->get($this->model, 'address', null, $attributes);
        $this->assertInstanceOf(Address::class, $address);
        $this->assertSame('Jl. Merdeka No. 1', $address->street);
        $this->assertSame('001', $address->rt);
        $this->assertSame('002', $address->rw);
        $this->assertSame('Sukamaju', $address->village);
        $this->assertSame('Cimahi', $address->sub_district);
        $this->assertSame('Bandung', $address->city);
        $this->assertSame('Jawa Barat', $address->province);
        $this->assertSame('40123', $address->postal_code);
    }

    public function test_set_returns_correct_attributes_array(): void
    {
        $address = new Address(
            street: 'Jl. Merdeka No. 1',
            rt: '001',
            rw: '002',
            village: 'Sukamaju',
            sub_district: 'Cimahi',
            city: 'Bandung',
            province: 'Jawa Barat',
            postal_code: '40123',
        );
        $result = $this->cast->set($this->model, 'address', $address, []);
        $expected = [
            'street' => 'Jl. Merdeka No. 1',
            'rt' => '001',
            'rw' => '002',
            'village' => 'Sukamaju',
            'sub_district' => 'Cimahi',
            'city' => 'Bandung',
            'province' => 'Jawa Barat',
            'postal_code' => '40123',
        ];
        $this->assertSame($expected, $result);
    }

    public function test_set_throws_exception_for_non_address_value(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The given value is not an Address instance.');
        $this->cast->set($this->model, 'address', 'some string', []);
    }
}
