<?php

declare(strict_types=1);

namespace Tests\Unit\Contexts\Beneficiary\Domain\Entities;

use Copie\Contexts\Beneficiary\Domain\Entities\FamilyCard;
use Copie\Shared\Domain\ValueObjects\Address;
use Copie\Shared\Domain\ValueObjects\DomainId;
use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class FamilyCardTest extends TestCase
{
    #[Test]
    public function test_create_generates_id_and_timestamp(): void
    {
        $familyCard = FamilyCard::create('123', 'Head');

        $this->assertInstanceOf(DomainId::class, $familyCard->id());
        $this->assertInstanceOf(DateTimeImmutable::class, $familyCard->createdAt());
        $this->assertNotInstanceOf(DateTimeImmutable::class, $familyCard->updatedAt());
    }

    #[Test]
    public function test_reconstitute_restores_from_persistence(): void
    {
        $domainId = DomainId::generate();
        $createdAt = new DateTimeImmutable('2024-01-01');
        $updatedAt = new DateTimeImmutable('2024-06-01');

        $familyCard = FamilyCard::reconstitute(
            $domainId,
            $createdAt,
            $updatedAt,
            '123',
            'Head'
        );

        $this->assertTrue($familyCard->id()->equals($domainId));
        $this->assertEquals($createdAt, $familyCard->createdAt());
        $this->assertEquals($updatedAt, $familyCard->updatedAt());
    }

    #[Test]
    public function test_number_returns_the_number(): void
    {
        $familyCard = FamilyCard::create('456', 'Head');
        $this->assertSame('456', $familyCard->number());
    }

    #[Test]
    public function test_head_of_family_name_returns_the_name(): void
    {
        $familyCard = FamilyCard::create('789', 'Head Of Family');
        $this->assertSame('Head Of Family', $familyCard->headOfFamilyName());
    }

    #[Test]
    public function test_address_returns_null_when_not_set(): void
    {
        $familyCard = FamilyCard::create('999', 'Head');
        $this->assertNotInstanceOf(Address::class, $familyCard->address());
    }

    #[Test]
    public function test_update_address_updates_timestamp(): void
    {
        $familyCard = FamilyCard::create('111', 'Head');
        $address = new Address('Street', '123', '456', 'Village', 'District', 'City', 'Province', '12345');
        $updatedAtBefore = $familyCard->updatedAt();

        $familyCard->updateAddress($address);

        $this->assertSame($address, $familyCard->address());
        $this->assertInstanceOf(DateTimeImmutable::class, $familyCard->updatedAt());
        $this->assertGreaterThanOrEqual(
            $updatedAtBefore instanceof DateTimeImmutable ? $updatedAtBefore->getTimestamp() : 0,
            $familyCard->updatedAt()->getTimestamp()
        );
    }

    #[Test]
    public function test_change_head_of_family_updates_name(): void
    {
        $familyCard = FamilyCard::create('222', 'Original Name');
        $updatedAtBefore = $familyCard->updatedAt();

        $familyCard->changeHeadOfFamily('New Name');

        $this->assertSame('New Name', $familyCard->headOfFamilyName());
        $this->assertInstanceOf(DateTimeImmutable::class, $familyCard->updatedAt());
        $this->assertGreaterThanOrEqual(
            $updatedAtBefore instanceof DateTimeImmutable ? $updatedAtBefore->getTimestamp() : 0,
            $familyCard->updatedAt()->getTimestamp()
        );
    }

    #[Test]
    public function test_change_head_of_family_with_empty_name_throws_exception(): void
    {
        $familyCard = FamilyCard::create('333', 'Head');
        $this->expectException(InvalidArgumentException::class);
        $familyCard->changeHeadOfFamily('');
    }

    #[Test]
    public function test_to_array_returns_correct_structure(): void
    {
        $address = new Address('Street', '123', '456', 'Village', 'District', 'City', 'Province', '12345');
        $familyCard = FamilyCard::create('444', 'Head', $address);
        $array = $familyCard->toArray();

        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('number', $array);
        $this->assertArrayHasKey('head_of_family_name', $array);
        $this->assertArrayHasKey('address', $array);

        $this->assertSame('444', $array['number']);
        $this->assertSame('Head', $array['head_of_family_name']);
        $this->assertIsArray($array['address']);
    }

    #[Test]
    public function test_equals_returns_true_for_same_id_and_class(): void
    {
        $domainId = DomainId::generate();
        $createdAt = new DateTimeImmutable('2024-01-01');
        $updatedAt = new DateTimeImmutable('2024-06-01');

        $familyCard1 = FamilyCard::reconstitute(
            $domainId,
            $createdAt,
            $updatedAt,
            '555',
            'Head'
        );

        $familyCard2 = FamilyCard::reconstitute(
            $domainId,
            $createdAt,
            $updatedAt,
            '555',
            'Head'
        );

        $this->assertTrue($familyCard1->equals($familyCard2));
    }

    #[Test]
    public function test_equals_returns_false_for_different_ids(): void
    {
        $domainId1 = DomainId::generate();
        $domainId2 = DomainId::generate();
        $createdAt = new DateTimeImmutable('2024-01-01');
        $updatedAt = new DateTimeImmutable('2024-06-01');

        $familyCard1 = FamilyCard::reconstitute(
            $domainId1,
            $createdAt,
            $updatedAt,
            '666',
            'Head'
        );

        $familyCard2 = FamilyCard::reconstitute(
            $domainId2,
            $createdAt,
            $updatedAt,
            '666',
            'Head'
        );

        $this->assertFalse($familyCard1->equals($familyCard2));
    }
}
