<?php

declare(strict_types=1);

namespace Tests\Unit\Contexts\Beneficiary\Domain\ValueObjects;

use Copie\Contexts\Beneficiary\Domain\ValueObjects\Name;
use Copie\Shared\Domain\Validator\ValidationException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class NameTest extends TestCase
{
    #[Test]
    public function valid_name_with_first_and_last_name(): void
    {
        $name = new Name('John', 'Doe');
        $this->assertSame('John', $name->firstName);
        $this->assertSame('Doe', $name->lastName);
    }

    #[Test]
    public function blank_first_name_throws_validation_exception(): void
    {
        $this->expectException(ValidationException::class);
        new Name('', 'Doe');
    }

    #[Test]
    public function blank_last_name_throws_validation_exception(): void
    {
        $this->expectException(ValidationException::class);
        new Name('John', '');
    }

    #[Test]
    public function full_name_returns_first_name_last_name(): void
    {
        $name = new Name('John', 'Doe');
        $this->assertSame('John Doe', $name->fullName());
    }

    #[Test]
    public function from_array_creates_name_from_array(): void
    {
        $data = ['firstName' => 'John', 'lastName' => 'Doe'];
        $name = Name::fromArray($data);
        $this->assertSame('John', $name->firstName);
        $this->assertSame('Doe', $name->lastName);
    }

    #[Test]
    public function to_array_returns_correct_array(): void
    {
        $name = new Name('John', 'Doe');
        $expected = ['firstName' => 'John', 'lastName' => 'Doe'];
        $this->assertSame($expected, $name->toArray());
    }

    #[Test]
    public function equals_returns_true_for_same_names(): void
    {
        $name1 = new Name('John', 'Doe');
        $name2 = new Name('John', 'Doe');
        $this->assertTrue($name1->equals($name2));
    }

    #[Test]
    public function equals_returns_false_for_different_names(): void
    {
        $name1 = new Name('John', 'Doe');
        $name2 = new Name('Jane', 'Smith');
        $this->assertFalse($name1->equals($name2));
    }
}
