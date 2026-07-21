<?php

declare(strict_types=1);

namespace Tests\Unit\Contexts\Beneficiary\Domain\Entities;

use Copie\Contexts\Beneficiary\Domain\Entities\FamilyCard;
use Copie\Shared\Domain\ValueObjects\Address;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class FamilyCardTest extends TestCase
{
    #[Test]
    public function test_create_returns_family_card_with_given_values(): void
    {
        $familyCard = FamilyCard::create('123', 'Head');

        $this->assertSame('123', $familyCard->number());
        $this->assertSame('Head', $familyCard->headOfFamilyName());
        $this->assertNull($familyCard->address());
    }

    #[Test]
    public function test_create_with_empty_head_of_family_name_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Head of family name must not be empty.');

        FamilyCard::create('123', '');
    }

    #[Test]
    public function test_create_with_whitespace_only_name_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Head of family name must not be empty.');

        FamilyCard::create('123', '   ');
    }

    #[Test]
    public function test_create_with_address(): void
    {
        $address = new Address('Jl. Test', '01', '02', 'Village', 'District', 'City', 'Province', '12345');
        $familyCard = FamilyCard::create('123', 'Head', $address);

        $this->assertNotNull($familyCard->address());
        $this->assertTrue($familyCard->address()->equals($address));
    }

    #[Test]
    public function test_to_array_returns_correct_structure(): void
    {
        $address = new Address('Jl. Test', '01', '02', 'Village', 'District', 'City', 'Province', '12345');
        $familyCard = FamilyCard::create('123', 'Head', $address);
        $array = $familyCard->toArray();

        $this->assertArrayHasKey('number', $array);
        $this->assertArrayHasKey('head_of_family_name', $array);
        $this->assertArrayHasKey('address', $array);
        $this->assertArrayNotHasKey('id', $array);
        $this->assertIsArray($array['address']);
        $this->assertSame('Head', $array['head_of_family_name']);
    }

    #[Test]
    public function test_to_array_without_address(): void
    {
        $familyCard = FamilyCard::create('123', 'Head');
        $array = $familyCard->toArray();

        $this->assertNull($array['address']);
    }

    #[Test]
    public function test_equals_returns_true_for_same_values(): void
    {
        $address = new Address('Jl. Test', '01', '02', 'Village', 'District', 'City', 'Province', '12345');
        $familyCard1 = FamilyCard::create('123', 'Head', $address);
        $familyCard2 = FamilyCard::create('123', 'Head', $address);

        $this->assertTrue($familyCard1->equals($familyCard2));
    }

    #[Test]
    public function test_equals_returns_false_for_different_values(): void
    {
        $familyCard1 = FamilyCard::create('123', 'Head');
        $familyCard2 = FamilyCard::create('456', 'Other');

        $this->assertFalse($familyCard1->equals($familyCard2));
    }

    #[Test]
    public function test_equals_returns_false_when_address_differs(): void
    {
        $address1 = new Address('Jl. A', '01', '02', 'Village', 'District', 'City', 'Province', '12345');
        $address2 = new Address('Jl. B', '01', '02', 'Village', 'District', 'City', 'Province', '12345');
        $familyCard1 = FamilyCard::create('123', 'Head', $address1);
        $familyCard2 = FamilyCard::create('123', 'Head', $address2);

        $this->assertFalse($familyCard1->equals($familyCard2));
    }

    #[Test]
    public function test_equals_returns_false_when_one_has_address_and_other_does_not(): void
    {
        $address = new Address('Jl. A', '01', '02', 'Village', 'District', 'City', 'Province', '12345');
        $familyCard1 = FamilyCard::create('123', 'Head', $address);
        $familyCard2 = FamilyCard::create('123', 'Head');

        $this->assertFalse($familyCard1->equals($familyCard2));
    }
}
