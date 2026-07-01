<?php

namespace Tests\Unit\Casts;

use App\Casts\AddressCast;
use App\ValueObjects\Address;
use Illuminate\Database\Eloquent\Model;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AddressCastTest extends TestCase
{
    private Model $model;

    private AddressCast $cast;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cast = new AddressCast;

        $this->model = new class extends Model {};
    }

    #[Test]
    public function it_casts_json_string_from_database_to_address_object()
    {
        $json = json_encode([
            'street' => 'Jl. Merdeka No. 10',
            'rt' => '01',
            'rw' => '02',
            'village' => 'Gambir',
            'district' => 'Gambir',
            'city' => 'Jakarta Pusat',
            'province' => 'DKI Jakarta',
            'postal_code' => '10110',
        ]);

        $result = $this->cast->get($this->model, 'address', $json, []);

        $this->assertInstanceOf(Address::class, $result);
        $this->assertEquals('Jl. Merdeka No. 10', $result->toArray()['street']);
        $this->assertEquals('10110', $result->toArray()['postal_code']);
    }

    #[Test]
    public function it_returns_null_when_database_value_is_empty()
    {
        $this->assertNull($this->cast->get($this->model, 'address', null, []));
        $this->assertNull($this->cast->get($this->model, 'address', '', []));
    }

    #[Test]
    public function it_serializes_address_object_to_json_string_for_database()
    {
        $address = new Address(
            'Jl. Merdeka No. 10', '01', '02', 'Gambir', 'Gambir', 'Jakarta Pusat', 'DKI Jakarta', '10110'
        );

        $jsonResult = $this->cast->set($this->model, 'address', $address, []);

        $this->assertJson($jsonResult);

        $decoded = json_decode($jsonResult, true);
        $this->assertEquals('Jl. Merdeka No. 10', $decoded['street']);
    }

    #[Test]
    public function it_returns_null_when_setting_invalid_or_null_value()
    {
        $this->assertNull($this->cast->set($this->model, 'address', null, []));
    }

    #[Test]
    public function it_returns_null_when_database_json_is_invalid()
    {
        $invalidJson = 'no-json';

        $result = $this->cast->get($this->model, 'address', $invalidJson, []);

        $this->assertNull($result);
    }

    #[Test]
    public function it_can_serialize_raw_array_to_json_string()
    {
        $arrayData = [
            'street' => 'Jl. Kebon Jeruk',
            'rt' => '05',
            'rw' => '06',
            'village' => 'Sukabumi',
            'district' => 'Kebon Jeruk',
            'city' => 'Jakarta Barat',
            'province' => 'DKI Jakarta',
            'postal_code' => '11540',
        ];

        $jsonResult = $this->cast->set($this->model, 'address', $arrayData, []);

        $this->assertJson($jsonResult);
        $this->assertEquals('Jl. Kebon Jeruk', json_decode($jsonResult, true)['street']);
    }

    #[Test]
    public function it_throws_exception_when_setting_invalid_data_type()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->cast->set($this->model, 'address', 12345, []);
    }
}
