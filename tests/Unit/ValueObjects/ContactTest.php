<?php

namespace Tests\Unit\ValueObjects;

use App\ValueObjects\Contact;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ContactTest extends TestCase
{
    #[Test]
    public function it_can_create_contact_with_phone_only(): void
    {
        $contact = new Contact('081234567890');

        $this->assertInstanceOf(Contact::class, $contact);
        $this->assertEquals('081234567890', $contact->toArray()['phone']);
    }

    #[Test]
    public function it_can_create_contact_with_plus62_phone(): void
    {
        $contact = new Contact('+6281234567890');

        $this->assertInstanceOf(Contact::class, $contact);
    }

    #[Test]
    public function it_throws_on_invalid_phone(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Contact('12345');
    }

    #[Test]
    public function it_throws_on_invalid_email(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Contact('081234567890', 'invalid-email');
    }

    #[Test]
    public function it_can_create_with_valid_email(): void
    {
        $contact = new Contact('081234567890', 'test@example.com');

        $this->assertEquals('test@example.com', $contact->toArray()['emailAddress']);
    }

    #[Test]
    public function it_can_convert_to_array(): void
    {
        $contact = new Contact('081234567890', 'test@example.com', null, 'Some address');
        $array = $contact->toArray();

        $this->assertEquals('081234567890', $array['phone']);
        $this->assertEquals('test@example.com', $array['emailAddress']);
        $this->assertNull($array['website']);
        $this->assertEquals('Some address', $array['address']);
    }

    #[Test]
    public function it_can_create_from_array(): void
    {
        $data = [
            'phone' => '+6281234567890',
            'emailAddress' => 'user@domain.com',
        ];
        $contact = Contact::fromArray($data);

        $this->assertInstanceOf(Contact::class, $contact);
        $this->assertEquals('+6281234567890', $contact->toArray()['phone']);
        $this->assertEquals('user@domain.com', $contact->toArray()['emailAddress']);
    }
}
