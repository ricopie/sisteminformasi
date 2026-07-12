<?php

namespace Tests\Unit\DTOs;

use Application\Beneficiaries\DTOs\BeneficiaryData as BeneficiaryDataDto;
use Application\Beneficiaries\DTOs\FamilyCardData as FamilyCardDataDto;
use Application\Beneficiaries\DTOs\GuardianData as GuardianDataDto;
use Domain\Beneficiaries\ValueObjects\Enum\BeneficiaryType;
use Domain\Beneficiaries\ValueObjects\Enum\GuardianRelationship;
use Domain\Beneficiaries\ValueObjects\Enum\NationalIdentityNumber;
use PHPUnit\Framework\Attributes\Test;
use Shared\ValueObjects\Enum\Gender;
use Tests\TestCase;

class BeneficiaryDataTest extends TestCase
{
    #[Test]
    public function it_can_create_via_constructor_with_all_fields(): void
    {
        $familyCardData = [
            'number' => '1234567890',
            'head_of_family_name' => 'John Doe',
            'address' => [
                'street' => '123 Main St',
                'rt' => '01',
                'rw' => '02',
                'village' => 'Central',
                'district' => 'District',
                'city' => 'City',
                'province' => 'Province',
                'postal_code' => '12345',
            ],
        ];

        $guardianData = [
            'person' => [
                'name' => 'Jane Doe',
                'occupation' => 'Teacher',
            ],
            'relationship' => 'father',
        ];

        $nik = new NationalIdentityNumber('1234567890123456');
        $familyCard = new FamilyCardDataDto($familyCardData['number'], $familyCardData['head_of_family_name'], $familyCardData['address']);
        $guardian = new GuardianDataDto($guardianData['person'], GuardianRelationship::FATHER);

        $beneficiary = new BeneficiaryDataDto(
            nik: $nik,
            type: BeneficiaryType::CHILD,
            fullName: 'John Doe',
            nickName: 'Johnny',
            birthPlace: 'Jakarta',
            birthDate: '2010-01-01',
            gender: Gender::MALE,
            familyCard: $familyCard,
            specificAttributes: ['education' => ['school' => 'SDN 1'], 'hobbies' => ['reading']],
            guardians: [$guardian],
        );

        $this->assertInstanceOf(NationalIdentityNumber::class, $beneficiary->nik);
        $this->assertSame('1234567890123456', $beneficiary->nik->value);
        $this->assertSame(BeneficiaryType::CHILD->value, $beneficiary->type->value);
        $this->assertSame('John Doe', $beneficiary->fullName);
        $this->assertSame('Johnny', $beneficiary->nickName);
        $this->assertSame('Jakarta', $beneficiary->birthPlace);
        $this->assertSame('2010-01-01', $beneficiary->birthDate);
        $this->assertSame(Gender::MALE->value, $beneficiary->gender->value);
        $this->assertInstanceOf(FamilyCardDataDto::class, $beneficiary->familyCard);
        $this->assertSame('1234567890', $beneficiary->familyCard->number);
        $this->assertSame(['education' => ['school' => 'SDN 1'], 'hobbies' => ['reading']], $beneficiary->specificAttributes);
        $this->assertCount(1, $beneficiary->guardians);
        $this->assertInstanceOf(GuardianDataDto::class, $beneficiary->guardians[0]);
        $this->assertSame('Jane Doe', $beneficiary->guardians[0]->person['name']);
        $this->assertSame('father', $beneficiary->guardians[0]->relationship->value);
    }

    #[Test]
    public function it_can_create_via_static_from_with_array(): void
    {
        $data = [
            'nik' => new NationalIdentityNumber('1234567890123456'),
            'type' => BeneficiaryType::CHILD,
            'fullName' => 'John Doe',
            'nickName' => 'Johnny',
            'birthPlace' => 'Jakarta',
            'birthDate' => '2010-01-01',
            'gender' => Gender::MALE,
            'familyCard' => FamilyCardDataDto::from([
                'number' => '1234567890',
                'head_of_family_name' => 'John Doe',
                'address' => [
                    'street' => '123 Main St',
                    'rt' => '01',
                    'rw' => '02',
                    'village' => 'Central',
                    'district' => 'District',
                    'city' => 'City',
                    'province' => 'Province',
                    'postal_code' => '12345',
                ],
            ]),
            'specificAttributes' => ['education' => ['school' => 'SDN 1'], 'hobbies' => ['reading']],
            'guardians' => [GuardianDataDto::from([
                'person' => [
                    'name' => 'Jane Doe',
                    'occupation' => 'Teacher',
                ],
                'relationship' => 'father',
            ])],
        ];

        $beneficiary = BeneficiaryDataDto::from($data);

        $this->assertInstanceOf(NationalIdentityNumber::class, $beneficiary->nik);
        $this->assertSame('1234567890123456', $beneficiary->nik->value);
        $this->assertSame(BeneficiaryType::CHILD->value, $beneficiary->type->value);
        $this->assertSame('John Doe', $beneficiary->fullName);
        $this->assertSame('Johnny', $beneficiary->nickName);
        $this->assertSame('Jakarta', $beneficiary->birthPlace);
        $this->assertSame('2010-01-01', $beneficiary->birthDate);
        $this->assertSame(Gender::MALE->value, $beneficiary->gender->value);
        $this->assertInstanceOf(FamilyCardDataDto::class, $beneficiary->familyCard);
        $this->assertSame('1234567890', $beneficiary->familyCard->number);
        $this->assertSame(['education' => ['school' => 'SDN 1'], 'hobbies' => ['reading']], $beneficiary->specificAttributes);
        $this->assertCount(1, $beneficiary->guardians);
        $this->assertInstanceOf(GuardianDataDto::class, $beneficiary->guardians[0]);
        $this->assertSame('Jane Doe', $beneficiary->guardians[0]->person['name']);
        $this->assertSame('father', $beneficiary->guardians[0]->relationship->value);
    }

    #[Test]
    public function it_to_array_returns_correct_structure(): void
    {
        $familyCardData = [
            'number' => '1234567890',
            'head_of_family_name' => 'John Doe',
            'address' => [
                'street' => '123 Main St',
                'rt' => '01',
                'rw' => '02',
                'village' => 'Central',
                'district' => 'District',
                'city' => 'City',
                'province' => 'Province',
                'postal_code' => '12345',
            ],
        ];

        $guardianData = [
            'person' => [
                'name' => 'Jane Doe',
                'occupation' => 'Teacher',
            ],
            'relationship' => 'father',
        ];

        $nik = new NationalIdentityNumber('1234567890123456');
        $familyCard = new FamilyCardDataDto($familyCardData['number'], $familyCardData['head_of_family_name'], $familyCardData['address']);
        $guardian = new GuardianDataDto($guardianData['person'], GuardianRelationship::FATHER);

        $beneficiary = new BeneficiaryDataDto(
            nik: $nik,
            type: BeneficiaryType::CHILD,
            fullName: 'John Doe',
            nickName: 'Johnny',
            birthPlace: 'Jakarta',
            birthDate: '2010-01-01',
            gender: Gender::MALE,
            familyCard: $familyCard,
            specificAttributes: ['education' => ['school' => 'SDN 1'], 'hobbies' => ['reading']],
            guardians: [$guardian],
        );

        $arrayData = $beneficiary->toArray();

        $this->assertInstanceOf(NationalIdentityNumber::class, $arrayData['nik']);
        $this->assertSame('1234567890123456', $arrayData['nik']->value);
        $this->assertSame('child', $arrayData['type']);
        $this->assertSame('John Doe', $arrayData['fullName']);
        $this->assertSame('Johnny', $arrayData['nickName']);
        $this->assertSame('Jakarta', $arrayData['birthPlace']);
        $this->assertSame('2010-01-01', $arrayData['birthDate']);
        $this->assertSame('male', $arrayData['gender']);
        $this->assertIsArray($arrayData['familyCard']);
        $this->assertSame('1234567890', $arrayData['familyCard']['number']);
        $this->assertSame('John Doe', $arrayData['familyCard']['head_of_family_name']);
        $this->assertSame(['education' => ['school' => 'SDN 1'], 'hobbies' => ['reading']], $arrayData['specificAttributes']);
        $this->assertCount(1, $arrayData['guardians']);
        $this->assertIsArray($arrayData['guardians'][0]);
        $this->assertSame('Jane Doe', $arrayData['guardians'][0]['person']['name']);
        $this->assertSame('father', $arrayData['guardians'][0]['relationship']);
    }

    #[Test]
    public function it_can_create_with_minimal_data(): void
    {
        $nik = new NationalIdentityNumber('1234567890123456');

        $beneficiary = new BeneficiaryDataDto(
            nik: $nik,
            type: BeneficiaryType::CHILD,
            fullName: 'John Doe',
            nickName: null,
            birthPlace: 'Jakarta',
            birthDate: '2010-01-01',
            gender: Gender::MALE,
        );

        $this->assertInstanceOf(NationalIdentityNumber::class, $beneficiary->nik);
        $this->assertSame('1234567890123456', $beneficiary->nik->value);
        $this->assertSame(BeneficiaryType::CHILD->value, $beneficiary->type->value);
        $this->assertSame('John Doe', $beneficiary->fullName);
        $this->assertNull($beneficiary->nickName);
        $this->assertSame('Jakarta', $beneficiary->birthPlace);
        $this->assertSame('2010-01-01', $beneficiary->birthDate);
        $this->assertSame(Gender::MALE->value, $beneficiary->gender->value);
        $this->assertNull($beneficiary->familyCard);
        $this->assertNull($beneficiary->specificAttributes);
        $this->assertNull($beneficiary->guardians);
    }

    #[Test]
    public function it_to_array_with_null_optional_fields(): void
    {
        $nik = new NationalIdentityNumber('1234567890123456');

        $beneficiary = new BeneficiaryDataDto(
            nik: $nik,
            type: BeneficiaryType::CHILD,
            fullName: 'John Doe',
            nickName: null,
            birthPlace: 'Jakarta',
            birthDate: '2010-01-01',
            gender: Gender::MALE,
        );

        $arrayData = $beneficiary->toArray();

        $this->assertInstanceOf(NationalIdentityNumber::class, $arrayData['nik']);
        $this->assertSame('1234567890123456', $arrayData['nik']->value);
        $this->assertSame('child', $arrayData['type']);
        $this->assertSame('John Doe', $arrayData['fullName']);
        $this->assertNull($arrayData['nickName']);
        $this->assertSame('Jakarta', $arrayData['birthPlace']);
        $this->assertSame('2010-01-01', $arrayData['birthDate']);
        $this->assertSame('male', $arrayData['gender']);
        $this->assertNull($arrayData['familyCard']);
        $this->assertNull($arrayData['specificAttributes']);
        $this->assertNull($arrayData['guardians']);
    }
}
