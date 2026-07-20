<?php

declare(strict_types=1);

namespace Tests\Unit\Contexts\Beneficiary\Application\Command;

use Copie\Contexts\Beneficiary\Application\Command\CreateBeneficiaryCommand;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class CreateBeneficiaryCommandTest extends TestCase
{
    #[Test]
    public function test_from_array_creates_command_with_all_fields(): void
    {
        $data = [
            'nik' => '1234567890',
            'type' => 'INDIVIDUAL',
            'firstName' => 'John',
            'lastName' => 'Doe',
            'nickName' => 'Johnny',
            'birthPlace' => 'Jakarta',
            'birthDate' => '1990-01-01',
            'gender' => 'MALE',
            'familyCardNumber' => '12345678',
            'headOfFamilyName' => 'Family Head',
            'specificAttributes' => ['age' => 30],
        ];

        $createBeneficiaryCommand = CreateBeneficiaryCommand::fromArray($data);

        $this->assertSame($data['nik'], $createBeneficiaryCommand->nik);
        $this->assertSame($data['type'], $createBeneficiaryCommand->type);
        $this->assertSame($data['firstName'], $createBeneficiaryCommand->firstName);
        $this->assertSame($data['lastName'], $createBeneficiaryCommand->lastName);
        $this->assertSame($data['nickName'], $createBeneficiaryCommand->nickName);
        $this->assertSame($data['birthPlace'], $createBeneficiaryCommand->birthPlace);
        $this->assertSame($data['birthDate'], $createBeneficiaryCommand->birthDate);
        $this->assertSame($data['gender'], $createBeneficiaryCommand->gender);
        $this->assertSame($data['familyCardNumber'], $createBeneficiaryCommand->familyCardNumber);
        $this->assertSame($data['headOfFamilyName'], $createBeneficiaryCommand->headOfFamilyName);
        $this->assertSame($data['specificAttributes'], $createBeneficiaryCommand->specificAttributes);
    }

    #[Test]
    public function test_from_array_defaults_optional_fields_to_null(): void
    {
        $data = [
            'nik' => '1234567890',
            'type' => 'INDIVIDUAL',
            'firstName' => 'John',
            'lastName' => 'Doe',
            'birthPlace' => 'Jakarta',
            'birthDate' => '1990-01-01',
            'gender' => 'MALE',
            'familyCardNumber' => '12345678',
            'headOfFamilyName' => 'Family Head',
        ];

        $createBeneficiaryCommand = CreateBeneficiaryCommand::fromArray($data);

        $this->assertNull($createBeneficiaryCommand->nickName);
        $this->assertNull($createBeneficiaryCommand->specificAttributes);
    }

    #[Test]
    public function test_to_array_returns_all_fields(): void
    {
        $createBeneficiaryCommand = new CreateBeneficiaryCommand(
            nik: '1234567890',
            type: 'INDIVIDUAL',
            firstName: 'John',
            lastName: 'Doe',
            nickName: 'Johnny',
            birthPlace: 'Jakarta',
            birthDate: '1990-01-01',
            gender: 'MALE',
            familyCardNumber: '12345678',
            headOfFamilyName: 'Family Head',
            specificAttributes: ['age' => 30],
        );

        $expected = [
            'nik' => '1234567890',
            'type' => 'INDIVIDUAL',
            'firstName' => 'John',
            'lastName' => 'Doe',
            'nickName' => 'Johnny',
            'birthPlace' => 'Jakarta',
            'birthDate' => '1990-01-01',
            'gender' => 'MALE',
            'familyCardNumber' => '12345678',
            'headOfFamilyName' => 'Family Head',
            'specificAttributes' => ['age' => 30],
        ];

        $this->assertSame($expected, $createBeneficiaryCommand->toArray());
    }

    #[Test]
    public function test_round_trip_from_array_to_array(): void
    {
        $original = [
            'nik' => '1234567890',
            'type' => 'INDIVIDUAL',
            'firstName' => 'John',
            'lastName' => 'Doe',
            'nickName' => 'Johnny',
            'birthPlace' => 'Jakarta',
            'birthDate' => '1990-01-01',
            'gender' => 'MALE',
            'familyCardNumber' => '12345678',
            'headOfFamilyName' => 'Family Head',
            'specificAttributes' => ['age' => 30],
        ];

        $createBeneficiaryCommand = CreateBeneficiaryCommand::fromArray($original);
        $result = $createBeneficiaryCommand->toArray();

        $this->assertSame($original, $result);
    }
}
