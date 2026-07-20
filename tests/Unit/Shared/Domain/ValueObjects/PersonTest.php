<?php

declare(strict_types=1);

namespace Tests\Unit\Shared\Domain\ValueObjects;

use Copie\Shared\Domain\Validator\ValidationException;
use Copie\Shared\Domain\ValueObjects\Address;
use Copie\Shared\Domain\ValueObjects\Contact;
use Copie\Shared\Domain\ValueObjects\Person;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class PersonTest extends TestCase
{
    #[Test]
    public function valid_person_with_name_only(): void
    {
        $person = new Person('John Doe');
        $this->assertSame('John Doe', $person->name);
        $this->assertNull($person->occupation);
        $this->assertNull($person->education);
        $this->assertNotInstanceOf(Address::class, $person->address);
        $this->assertNotInstanceOf(Contact::class, $person->contact);
    }

    #[Test]
    public function valid_person_with_all_fields(): void
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
        $contact = new Contact('081234567890', 'test@example.com');
        $person = new Person('John Doe', 'Engineer', 'S1', $address, $contact);
        $this->assertSame('John Doe', $person->name);
        $this->assertSame('Engineer', $person->occupation);
        $this->assertSame('S1', $person->education);
        $this->assertSame($address, $person->address);
        $this->assertSame($contact, $person->contact);
    }

    #[Test]
    public function blank_name_throws_validation_exception(): void
    {
        $this->expectException(ValidationException::class);
        new Person('');
    }

    #[Test]
    public function from_array_creates_person_from_array(): void
    {
        $data = [
            'name' => 'John Doe',
            'occupation' => 'Engineer',
            'education' => 'S1',
            'address' => [
                'street' => 'Jl. Merdeka',
                'rt' => '01',
                'rw' => '02',
                'village' => 'Kampung A',
                'district' => 'Kec. A',
                'city' => 'Kota A',
                'province' => 'Provinsi A',
                'postalCode' => '12345',
            ],
            'contact' => [
                'phone' => '081234567890',
                'emailAddress' => 'test@example.com',
            ],
        ];
        $person = Person::fromArray($data);
        $this->assertSame($data['name'], $person->name);
        $this->assertSame($data['occupation'], $person->occupation);
        $this->assertSame($data['education'], $person->education);
        $this->assertInstanceOf(Address::class, $person->address);
        $this->assertSame($data['address']['street'], $person->address->street);
        $this->assertInstanceOf(Contact::class, $person->contact);
        $this->assertSame($data['contact']['phone'], $person->contact->phone);
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
        $contact = new Contact('081234567890', 'test@example.com');
        $person = new Person('John Doe', 'Engineer', 'S1', $address, $contact);
        $expected = [
            'name' => 'John Doe',
            'occupation' => 'Engineer',
            'education' => 'S1',
            'address' => $address->toArray(),
            'contact' => $contact->toArray(),
        ];
        $this->assertSame($expected, $person->toArray());
    }

    #[Test]
    public function equals_returns_true_for_same_values(): void
    {
        $person1 = new Person('John Doe');
        $person2 = new Person('John Doe');
        $this->assertTrue($person1->equals($person2));
    }

    #[Test]
    public function equals_returns_false_for_different_values(): void
    {
        $person1 = new Person('John Doe');
        $person2 = new Person('Jane Doe');
        $this->assertFalse($person1->equals($person2));
    }

    #[Test]
    public function null_address_is_allowed(): void
    {
        $person = new Person('John Doe');
        $this->assertNotInstanceOf(Address::class, $person->address);
        $this->assertNotInstanceOf(Contact::class, $person->contact);
    }

    #[Test]
    public function null_contact_is_allowed(): void
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
        $person = new Person('John Doe', address: $address);
        $this->assertSame($address, $person->address);
        $this->assertNotInstanceOf(Contact::class, $person->contact);
    }
}
