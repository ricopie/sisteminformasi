<?php

namespace Tests\Unit\ValueObjects;

use App\ValueObjects\Address;
use App\ValueObjects\Contact;
use App\ValueObjects\Person;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PersonTest extends TestCase
{
    #[Test]
    public function it_can_create_person_with_minimal_data(): void
    {
        $person = new Person('John Doe', null, null, null, null);

        $this->assertInstanceOf(Person::class, $person);
        $this->assertEquals('John Doe', $person->toArray()['name']);
    }

    #[Test]
    public function it_throws_on_empty_name(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Person('', null, null, null, null);
    }

    #[Test]
    public function it_can_create_with_all_fields(): void
    {
        $address = new Address('Jl. Merdeka', '01', '02', 'Kelurahan', 'Kecamatan', 'Kota', 'Provinsi', '12345');
        $contact = new Contact('081234567890', 'john@example.com');

        $person = new Person('John Doe', 'Engineer', null, $address, $contact);
        $array = $person->toArray();

        $this->assertEquals('John Doe', $array['name']);
        $this->assertEquals('Engineer', $array['occupation']);
        $this->assertNull($array['education']);
        $this->assertEquals($address->toArray(), $array['address']);
        $this->assertEquals($contact->toArray(), $array['contact']);
    }

    #[Test]
    public function it_can_create_from_array(): void
    {
        $data = [
            'name' => 'Jane Doe',
            'occupation' => 'Teacher',
            'address' => [
                'street' => 'Jl. Sudirman',
                'rt' => '03',
                'rw' => '04',
                'village' => 'Kelurahan',
                'district' => 'Kecamatan',
                'city' => 'Kota',
                'province' => 'Provinsi',
                'postal_code' => '54321',
            ],
            'contact' => [
                'phone' => '+6281234567890',
                'emailAddress' => 'jane@example.com',
            ],
        ];

        $person = Person::fromArray($data);

        $this->assertInstanceOf(Person::class, $person);
        $this->assertEquals('Jane Doe', $person->toArray()['name']);
    }
}
