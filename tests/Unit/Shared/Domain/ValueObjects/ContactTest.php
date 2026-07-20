<?php

declare(strict_types=1);

namespace Tests\Unit\Shared\Domain\ValueObjects;

use Copie\Shared\Domain\Validator\ValidationException;
use Copie\Shared\Domain\ValueObjects\Contact;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ContactTest extends TestCase
{
    #[Test]
    public function valid_contact_with_phone_only(): void
    {
        $contact = new Contact('081234567890');
        $this->assertSame('081234567890', $contact->phone);
        $this->assertNull($contact->emailAddress);
        $this->assertNull($contact->website);
        $this->assertNull($contact->addressText);
    }

    #[Test]
    public function valid_contact_with_phone_and_email(): void
    {
        $contact = new Contact('081234567890', 'test@example.com');
        $this->assertSame('081234567890', $contact->phone);
        $this->assertSame('test@example.com', $contact->emailAddress);
        $this->assertNull($contact->website);
        $this->assertNull($contact->addressText);
    }

    #[Test]
    public function blank_phone_throws_validation_exception(): void
    {
        $this->expectException(ValidationException::class);
        new Contact('');
    }

    #[Test]
    public function invalid_phone_format_throws_validation_exception(): void
    {
        $this->expectException(ValidationException::class);
        new Contact('invalid-phone');
    }

    #[Test]
    public function valid_phone_formats_accepted(): void
    {
        $contact1 = new Contact('081234567890');
        $this->assertSame('081234567890', $contact1->phone);

        $contact2 = new Contact('+6281234567890');
        $this->assertSame('+6281234567890', $contact2->phone);
    }

    #[Test]
    public function invalid_email_format_throws_validation_exception(): void
    {
        $this->expectException(ValidationException::class);
        new Contact('081234567890', 'invalid-email');
    }

    #[Test]
    public function valid_email_is_accepted(): void
    {
        $contact = new Contact('081234567890', 'test@example.com');
        $this->assertSame('test@example.com', $contact->emailAddress);
    }

    #[Test]
    public function null_email_is_allowed(): void
    {
        $contact = new Contact('081234567890');
        $this->assertNull($contact->emailAddress);
    }

    #[Test]
    public function null_website_is_allowed(): void
    {
        $contact = new Contact('081234567890');
        $this->assertNull($contact->website);
    }

    #[Test]
    public function from_array_creates_contact_from_array(): void
    {
        $data = [
            'phone' => '081234567890',
            'emailAddress' => 'test@example.com',
            'website' => 'https://example.com',
            'addressText' => 'Some address',
        ];
        $contact = Contact::fromArray($data);
        $this->assertSame($data['phone'], $contact->phone);
        $this->assertSame($data['emailAddress'], $contact->emailAddress);
        $this->assertSame($data['website'], $contact->website);
        $this->assertSame($data['addressText'], $contact->addressText);
    }

    #[Test]
    public function to_array_returns_correct_array(): void
    {
        $contact = new Contact('081234567890', 'test@example.com', 'https://example.com', 'Some address');
        $expected = [
            'phone' => '081234567890',
            'emailAddress' => 'test@example.com',
            'website' => 'https://example.com',
            'addressText' => 'Some address',
        ];
        $this->assertSame($expected, $contact->toArray());
    }

    #[Test]
    public function equals_returns_true_for_same_values(): void
    {
        $contact1 = new Contact('081234567890', 'test@example.com');
        $contact2 = new Contact('081234567890', 'test@example.com');
        $this->assertTrue($contact1->equals($contact2));
    }

    #[Test]
    public function equals_returns_false_for_different_values(): void
    {
        $contact1 = new Contact('081234567890', 'test@example.com');
        $contact2 = new Contact('081234567890', 'different@example.com');
        $this->assertFalse($contact1->equals($contact2));
    }
}
