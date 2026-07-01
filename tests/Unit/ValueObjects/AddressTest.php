<?php

namespace Tests\Unit\ValueObjects;

use App\ValueObjects\Address;
use PHPUnit\Framework\Attributes\Test;
use Tests\Faker\IndonesiaAddressHelper;
use Tests\TestCase;

class AddressTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['app.faker_locale' => 'id_ID']);
    }

    #[Test]
    public function it_can_convert_address_to_array()
    {
        $data = $this->fakeAddress();
        $address = new Address(...$data);

        $this->assertEquals($data, $address->toArray());
    }

    #[Test]
    public function it_can_validate_equality()
    {
        $data = $this->fakeAddress();
        $address = new Address(...$data);

        $this->assertTrue($address->equals(new Address(...$data)));

        $diffData = $data;
        $diffData['rt'] = $data['rt'] === '01' ? '02' : '01';

        $diffAddress = new Address(...$diffData);

        $this->assertFalse($address->equals($diffAddress));
    }

    private function fakeAddress(): array
    {
        return [
            'street' => fake()->streetAddress(),
            'rt' => str_pad(fake()->numberBetween(1, 15), 2, '0', STR_PAD_LEFT),
            'rw' => str_pad(fake()->numberBetween(1, 15), 2, '0', STR_PAD_LEFT),
            'village' => IndonesiaAddressHelper::village(),
            'district' => IndonesiaAddressHelper::district(),
            'city' => fake()->city(),
            'province' => IndonesiaAddressHelper::province(),
            'postal_code' => fake()->postcode(),
        ];
    }
}
