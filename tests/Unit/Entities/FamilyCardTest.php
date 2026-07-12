<?php

declare(strict_types=1);

namespace Tests\Unit\Entities;

use DateTimeImmutable;
use Domain\Beneficiaries\Entities\FamilyCard;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use Shared\ValueObjects\Address;
use Shared\ValueObjects\DomainId;
use Tests\TestCase;

final class FamilyCardTest extends TestCase
{
    #[Test]
    public function it_can_register_a_family_card(): void
    {
        $familyCard = FamilyCard::register('12345', 'John Doe');

        $this->assertSame('12345', $familyCard->number());
        $this->assertSame('John Doe', $familyCard->headOfFamilyName());
        $this->assertNull($familyCard->address());
        $this->assertInstanceOf(DomainId::class, $familyCard->id());
    }

    #[Test]
    public function it_can_register_with_address(): void
    {
        $address = new Address('Jl. Merdeka', '01', '02', 'Kelurahan', 'Kecamatan', 'Kota', 'Provinsi', '12345');

        $familyCard = FamilyCard::register('12345', 'John Doe', $address);

        $this->assertSame($address, $familyCard->address());
    }

    #[Test]
    public function it_throws_on_empty_number(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Family card number must not be empty.');

        FamilyCard::register('', 'John Doe');
    }

    #[Test]
    public function it_throws_on_empty_head_of_family_name(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Head of family name must not be empty.');

        FamilyCard::register('12345', '');
    }

    #[Test]
    public function it_trims_whitespace(): void
    {
        $familyCard = FamilyCard::register(' 12345 ', ' John Doe ');

        $this->assertSame('12345', $familyCard->number());
        $this->assertSame('John Doe', $familyCard->headOfFamilyName());
    }

    #[Test]
    public function it_can_reconstitute_from_persistence(): void
    {
        $domainId = new DomainId('01JX6N4Z4H95PD1AMD87B6XVK9');
        $createdAt = new DateTimeImmutable('2024-01-01');
        $updatedAt = null;
        $address = new Address('Jl. Merdeka', '01', '02', 'Kelurahan', 'Kecamatan', 'Kota', 'Provinsi', '12345');

        $familyCard = FamilyCard::reconstitute($domainId, $createdAt, $updatedAt, '12345', 'John Doe', $address);

        $this->assertSame($domainId, $familyCard->id());
        $this->assertSame($createdAt, $familyCard->createdAt());
        $this->assertSame($updatedAt, $familyCard->updatedAt());
        $this->assertSame('12345', $familyCard->number());
        $this->assertSame('John Doe', $familyCard->headOfFamilyName());
        $this->assertSame($address, $familyCard->address());
    }

    #[Test]
    public function it_can_reconstitute_without_address(): void
    {
        $domainId = new DomainId('01JX6N4Z4H95PD1AMD87B6XVK9');
        $createdAt = new DateTimeImmutable('2024-01-01');
        $updatedAt = null;

        $familyCard = FamilyCard::reconstitute($domainId, $createdAt, $updatedAt, '12345', 'John Doe');

        $this->assertSame($domainId, $familyCard->id());
        $this->assertSame($createdAt, $familyCard->createdAt());
        $this->assertSame($updatedAt, $familyCard->updatedAt());
        $this->assertSame('12345', $familyCard->number());
        $this->assertSame('John Doe', $familyCard->headOfFamilyName());
        $this->assertNull($familyCard->address());
    }

    #[Test]
    public function it_can_update_address(): void
    {
        $familyCard = FamilyCard::register('12345', 'John Doe');
        $updatedAtBefore = $familyCard->updatedAt();

        $newAddress = new Address('Jl. Test', '01', '02', 'Kelurahan Test', 'Kecamatan Test', 'Kota Test', 'Provinsi Test', '12345');
        $familyCard->updateAddress($newAddress);

        $this->assertSame($newAddress, $familyCard->address());
        $this->assertNotSame($updatedAtBefore, $familyCard->updatedAt());
        $this->assertNotNull($familyCard->updatedAt());
    }

    #[Test]
    public function it_can_change_head_of_family(): void
    {
        $familyCard = FamilyCard::register('12345', 'John Doe');
        $updatedAtBefore = $familyCard->updatedAt();

        $familyCard->changeHeadOfFamily('Jane Doe');

        $this->assertSame('Jane Doe', $familyCard->headOfFamilyName());
        $this->assertNotSame($updatedAtBefore, $familyCard->updatedAt());
        $this->assertNotNull($familyCard->updatedAt());
    }

    #[Test]
    public function it_throws_on_empty_head_of_family_name_when_changing(): void
    {
        $familyCard = FamilyCard::register('12345', 'John Doe');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Head of family name must not be empty.');

        $familyCard->changeHeadOfFamily('');
    }

    #[Test]
    public function it_can_serialize_to_array_with_address(): void
    {
        $address = new Address('Jl. Merdeka', '01', '02', 'Kelurahan', 'Kecamatan', 'Kota', 'Provinsi', '12345');
        $familyCard = FamilyCard::register('12345', 'John Doe', $address);

        $array = $familyCard->toArray();

        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('number', $array);
        $this->assertArrayHasKey('head_of_family_name', $array);
        $this->assertArrayHasKey('address', $array);

        $this->assertSame($familyCard->id()->value, $array['id']);
        $this->assertSame($familyCard->number(), $array['number']);
        $this->assertSame($familyCard->headOfFamilyName(), $array['head_of_family_name']);
        $this->assertSame($familyCard->address()->toArray(), $array['address']);
    }

    #[Test]
    public function it_can_serialize_to_array_without_address(): void
    {
        $familyCard = FamilyCard::register('12345', 'John Doe');

        $array = $familyCard->toArray();

        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('number', $array);
        $this->assertArrayHasKey('head_of_family_name', $array);
        $this->assertArrayHasKey('address', $array);

        $this->assertSame($familyCard->id()->value, $array['id']);
        $this->assertSame($familyCard->number(), $array['number']);
        $this->assertSame($familyCard->headOfFamilyName(), $array['head_of_family_name']);
        $this->assertNull($array['address']);
    }

    #[Test]
    public function it_is_not_equal_to_different_family_card_instance(): void
    {
        $familyCard1 = FamilyCard::register('12345', 'John Doe');
        $familyCard2 = FamilyCard::register('12345', 'John Doe');

        $this->assertNotEquals($familyCard1, $familyCard2);
    }

    #[Test]
    public function it_can_generate_new_id(): void
    {
        $id = FamilyCard::newId();

        $this->assertInstanceOf(DomainId::class, $id);
    }
}
