<?php

declare(strict_types=1);

namespace Tests\Unit\Contexts\Beneficiary\Application\Command;

use Copie\Contexts\Beneficiary\Application\Command\UpdateBeneficiaryCommand;
use Copie\Shared\Domain\Validator\ValidationException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class UpdateBeneficiaryCommandTest extends TestCase
{
    #[Test]
    public function test_from_array_creates_command_with_only_id(): void
    {
        $data = ['id' => '123'];

        $updateBeneficiaryCommand = UpdateBeneficiaryCommand::fromArray($data);

        $this->assertSame('123', $updateBeneficiaryCommand->id);
        $this->assertNull($updateBeneficiaryCommand->firstName);
        $this->assertNull($updateBeneficiaryCommand->lastName);
        $this->assertNull($updateBeneficiaryCommand->nickName);
        $this->assertNull($updateBeneficiaryCommand->birthPlace);
        $this->assertNull($updateBeneficiaryCommand->birthDate);
        $this->assertNull($updateBeneficiaryCommand->gender);
        $this->assertNull($updateBeneficiaryCommand->familyCardNumber);
        $this->assertNull($updateBeneficiaryCommand->headOfFamilyName);
        $this->assertNull($updateBeneficiaryCommand->type);
        $this->assertNull($updateBeneficiaryCommand->specificAttributes);
        $this->assertNull($updateBeneficiaryCommand->guardians);
        $this->assertNull($updateBeneficiaryCommand->isActive);
    }

    #[Test]
    public function test_from_array_creates_command_with_partial_fields(): void
    {
        $data = [
            'id' => '123',
            'firstName' => 'John',
            'gender' => 'FEMALE',
        ];

        $updateBeneficiaryCommand = UpdateBeneficiaryCommand::fromArray($data);

        $this->assertSame('123', $updateBeneficiaryCommand->id);
        $this->assertSame('John', $updateBeneficiaryCommand->firstName);
        $this->assertNull($updateBeneficiaryCommand->lastName);
        $this->assertNull($updateBeneficiaryCommand->nickName);
        $this->assertNull($updateBeneficiaryCommand->birthPlace);
        $this->assertNull($updateBeneficiaryCommand->birthDate);
        $this->assertSame('FEMALE', $updateBeneficiaryCommand->gender);
        $this->assertNull($updateBeneficiaryCommand->familyCardNumber);
        $this->assertNull($updateBeneficiaryCommand->headOfFamilyName);
        $this->assertNull($updateBeneficiaryCommand->type);
        $this->assertNull($updateBeneficiaryCommand->specificAttributes);
        $this->assertNull($updateBeneficiaryCommand->guardians);
        $this->assertNull($updateBeneficiaryCommand->isActive);
    }

    #[Test]
    public function test_from_array_throws_validation_exception_when_id_is_empty(): void
    {
        $this->expectException(ValidationException::class);

        $data = [
            'id' => '',
            'firstName' => 'John',
        ];

        UpdateBeneficiaryCommand::fromArray($data);
    }

    #[Test]
    public function test_from_array_creates_command_with_all_fields(): void
    {
        $data = [
            'id' => '123',
            'firstName' => 'John',
            'lastName' => 'Doe',
            'nickName' => 'Johnny',
            'birthPlace' => 'Jakarta',
            'birthDate' => '1990-01-01',
            'gender' => 'MALE',
            'familyCardNumber' => '12345678',
            'headOfFamilyName' => 'Family Head',
            'type' => 'INDIVIDUAL',
            'specificAttributes' => ['age' => 30],
            'guardians' => [['name' => 'Guardian']],
            'isActive' => true,
        ];

        $updateBeneficiaryCommand = UpdateBeneficiaryCommand::fromArray($data);

        $this->assertSame('123', $updateBeneficiaryCommand->id);
        $this->assertSame('John', $updateBeneficiaryCommand->firstName);
        $this->assertSame('Doe', $updateBeneficiaryCommand->lastName);
        $this->assertSame('Johnny', $updateBeneficiaryCommand->nickName);
        $this->assertSame('Jakarta', $updateBeneficiaryCommand->birthPlace);
        $this->assertSame('1990-01-01', $updateBeneficiaryCommand->birthDate);
        $this->assertSame('MALE', $updateBeneficiaryCommand->gender);
        $this->assertSame('12345678', $updateBeneficiaryCommand->familyCardNumber);
        $this->assertSame('Family Head', $updateBeneficiaryCommand->headOfFamilyName);
        $this->assertSame('INDIVIDUAL', $updateBeneficiaryCommand->type);
        $this->assertSame(['age' => 30], $updateBeneficiaryCommand->specificAttributes);
        $this->assertSame([['name' => 'Guardian']], $updateBeneficiaryCommand->guardians);
        $this->assertTrue($updateBeneficiaryCommand->isActive);
    }

    #[Test]
    public function test_to_array_returns_correct_structure(): void
    {
        $updateBeneficiaryCommand = new UpdateBeneficiaryCommand(
            id: '123',
            firstName: 'John',
            lastName: 'Doe',
            nickName: 'Johnny',
            birthPlace: 'Jakarta',
            birthDate: '1990-01-01',
            gender: 'MALE',
            familyCardNumber: '12345678',
            headOfFamilyName: 'Family Head',
            type: 'INDIVIDUAL',
            specificAttributes: ['age' => 30],
            guardians: [['name' => 'Guardian']],
            isActive: true,
        );

        $expected = [
            'id' => '123',
            'firstName' => 'John',
            'lastName' => 'Doe',
            'nickName' => 'Johnny',
            'birthPlace' => 'Jakarta',
            'birthDate' => '1990-01-01',
            'gender' => 'MALE',
            'familyCardNumber' => '12345678',
            'headOfFamilyName' => 'Family Head',
            'type' => 'INDIVIDUAL',
            'specificAttributes' => ['age' => 30],
            'guardians' => [['name' => 'Guardian']],
            'isActive' => true,
        ];

        $this->assertSame($expected, $updateBeneficiaryCommand->toArray());
    }
}
