<?php

declare(strict_types=1);

namespace Tests\Unit\Contexts\Beneficiary\Domain;

use Copie\Contexts\Beneficiary\Domain\Beneficiary;
use Copie\Contexts\Beneficiary\Domain\Entities\FamilyCard;
use Copie\Contexts\Beneficiary\Domain\Enums\BeneficiaryType;
use Copie\Contexts\Beneficiary\Domain\Enums\EducationStatus;
use Copie\Contexts\Beneficiary\Domain\Enums\GuardianRelationship;
use Copie\Contexts\Beneficiary\Domain\Events\BeneficiaryCreated;
use Copie\Contexts\Beneficiary\Domain\Events\BeneficiaryDeleted;
use Copie\Contexts\Beneficiary\Domain\Events\BeneficiaryUpdated;
use Copie\Contexts\Beneficiary\Domain\Exceptions\BeneficiaryAttributeException;
use Copie\Contexts\Beneficiary\Domain\ValueObjects\ChildAttributes;
use Copie\Contexts\Beneficiary\Domain\ValueObjects\Education;
use Copie\Contexts\Beneficiary\Domain\ValueObjects\Name;
use Copie\Contexts\Beneficiary\Domain\ValueObjects\NationalIdentityNumber;
use Copie\Shared\Domain\Enums\EducationLevel;
use Copie\Shared\Domain\Enums\Gender;
use Copie\Shared\Domain\ValueObjects\Address;
use Copie\Shared\Domain\ValueObjects\DomainId;
use Copie\Shared\Domain\ValueObjects\Person;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class BeneficiaryTest extends TestCase
{
    private NationalIdentityNumber $nationalIdentityNumber;

    private BeneficiaryType $beneficiaryType;

    private Name $name;

    private string $birthPlace;

    private string $birthDate;

    private Gender $gender;

    private FamilyCard $familyCard;

    private ChildAttributes $childAttributes;

    protected function setUp(): void
    {
        parent::setUp();
        $this->nationalIdentityNumber = new NationalIdentityNumber('1234567890123456');
        $this->beneficiaryType = BeneficiaryType::CHILD;
        $this->name = new Name('Budi', 'Santoso');
        $this->birthPlace = 'Jakarta';
        $this->birthDate = '2020-01-01';
        $this->gender = Gender::MALE;
        $this->familyCard = FamilyCard::create('1234567890123456', 'Head Of Family', new Address('Jl. Test', '123', '456', 'Village', 'District', 'City', 'Province', '12345'));
        $this->childAttributes = new ChildAttributes(
            education: new Education(
                level: EducationLevel::SENIOR_HIGH,
                status: EducationStatus::GRADUATED,
                schoolName: 'SMA Negeri 1',
                grade: 12,
                major: 'IPA',
                nisn: '1234567890'
            ),
            educationHistory: [],
            hobbies: ['Reading', 'Football', 'Music']
        );
    }

    #[Test]
    public function test_create_generates_id_timestamp_and_domain_event(): void
    {
        $beneficiary = Beneficiary::create(
            nationalIdentityNumber: $this->nationalIdentityNumber,
            beneficiaryType: $this->beneficiaryType,
            name: $this->name,
            nickName: null,
            birthPlace: $this->birthPlace,
            birthDate: $this->birthDate,
            gender: $this->gender,
            familyCard: $this->familyCard,
            specificAttributes: $this->childAttributes
        );

        $this->assertInstanceOf(DomainId::class, $beneficiary->id());
        $this->assertInstanceOf(DateTimeImmutable::class, $beneficiary->createdAt());
        $this->assertInstanceOf(DateTimeImmutable::class, $beneficiary->updatedAt());

        $events = $beneficiary->pullDomainEvents();
        $this->assertCount(1, $events);
        $this->assertInstanceOf(BeneficiaryCreated::class, $events[0]);
    }

    #[Test]
    public function test_create_with_child_type_and_child_attributes_is_valid(): void
    {
        $beneficiary = Beneficiary::create(
            nationalIdentityNumber: $this->nationalIdentityNumber,
            beneficiaryType: $this->beneficiaryType,
            name: $this->name,
            nickName: null,
            birthPlace: $this->birthPlace,
            birthDate: $this->birthDate,
            gender: $this->gender,
            familyCard: $this->familyCard,
            specificAttributes: $this->childAttributes
        );

        $this->assertInstanceOf(Beneficiary::class, $beneficiary);
        $this->assertTrue($beneficiary->isActive());
        $this->assertSame($this->beneficiaryType, $beneficiary->type());
    }

    #[Test]
    public function test_create_with_child_type_without_attributes_throws_exception(): void
    {
        $this->expectException(BeneficiaryAttributeException::class);
        Beneficiary::create(
            nationalIdentityNumber: $this->nationalIdentityNumber,
            beneficiaryType: $this->beneficiaryType,
            name: $this->name,
            nickName: null,
            birthPlace: $this->birthPlace,
            birthDate: $this->birthDate,
            gender: $this->gender,
            familyCard: $this->familyCard
        );
    }

    #[Test]
    public function test_create_with_elderly_type_and_child_attributes_throws_exception(): void
    {
        $elderlyType = BeneficiaryType::ELDERLY;
        $this->expectException(BeneficiaryAttributeException::class);
        Beneficiary::create(
            nationalIdentityNumber: $this->nationalIdentityNumber,
            beneficiaryType: $elderlyType,
            name: $this->name,
            nickName: null,
            birthPlace: $this->birthPlace,
            birthDate: $this->birthDate,
            gender: $this->gender,
            familyCard: $this->familyCard,
            specificAttributes: $this->childAttributes
        );
    }

    #[Test]
    public function test_reconstitute_restores_from_persistence(): void
    {
        $domainId = DomainId::generate();
        $createdAt = new DateTimeImmutable('2024-01-01');
        $updatedAt = new DateTimeImmutable('2024-06-01');

        $beneficiary = Beneficiary::reconstitute(
            $domainId,
            $createdAt,
            $updatedAt,
            $this->nationalIdentityNumber,
            $this->beneficiaryType,
            $this->name,
            nickName: null,
            birthPlace: $this->birthPlace,
            birthDate: $this->birthDate,
            gender: $this->gender,
            familyCard: $this->familyCard,
            isActive: true,
            specificAttributes: $this->childAttributes,
            guardians: []
        );

        $this->assertTrue($beneficiary->id()->equals($domainId));
        $this->assertEquals($createdAt, $beneficiary->createdAt());
        $this->assertEquals($updatedAt, $beneficiary->updatedAt());
        $this->assertSame($this->nationalIdentityNumber, $beneficiary->nik());
        $this->assertSame($this->beneficiaryType, $beneficiary->type());
        $this->assertSame($this->name, $beneficiary->name());
        $this->assertSame($this->familyCard, $beneficiary->familyCard());
        $this->assertTrue($beneficiary->isActive());
        $this->assertSame($this->childAttributes, $beneficiary->specificAttributes());
        $this->assertEmpty($beneficiary->guardians());
    }

    #[Test]
    public function test_nik_returns_national_identity_number(): void
    {
        $beneficiary = Beneficiary::create(
            nationalIdentityNumber: $this->nationalIdentityNumber,
            beneficiaryType: $this->beneficiaryType,
            name: $this->name,
            nickName: null,
            birthPlace: $this->birthPlace,
            birthDate: $this->birthDate,
            gender: $this->gender,
            familyCard: $this->familyCard,
            specificAttributes: $this->childAttributes
        );

        $this->assertSame($this->nationalIdentityNumber, $beneficiary->nik());
    }

    #[Test]
    public function test_type_returns_beneficiary_type(): void
    {
        $beneficiary = Beneficiary::create(
            nationalIdentityNumber: $this->nationalIdentityNumber,
            beneficiaryType: $this->beneficiaryType,
            name: $this->name,
            nickName: null,
            birthPlace: $this->birthPlace,
            birthDate: $this->birthDate,
            gender: $this->gender,
            familyCard: $this->familyCard,
            specificAttributes: $this->childAttributes
        );

        $this->assertSame($this->beneficiaryType, $beneficiary->type());
    }

    #[Test]
    public function test_rename_updates_name_and_emits_event(): void
    {
        $beneficiary = Beneficiary::create(
            nationalIdentityNumber: $this->nationalIdentityNumber,
            beneficiaryType: $this->beneficiaryType,
            name: $this->name,
            nickName: null,
            birthPlace: $this->birthPlace,
            birthDate: $this->birthDate,
            gender: $this->gender,
            familyCard: $this->familyCard,
            specificAttributes: $this->childAttributes
        );

        $beneficiary->pullDomainEvents(); // Clear creation event

        $newName = new Name('John', 'Doe');
        $updatedAtBefore = $beneficiary->updatedAt();

        $beneficiary->rename($newName, nickName: null);

        $this->assertSame($newName, $beneficiary->name());
        $this->assertInstanceOf(DateTimeImmutable::class, $beneficiary->updatedAt());
        $this->assertGreaterThanOrEqual(
            $updatedAtBefore instanceof DateTimeImmutable ? $updatedAtBefore->getTimestamp() : 0,
            $beneficiary->updatedAt()->getTimestamp()
        );

        $events = $beneficiary->pullDomainEvents();
        $this->assertCount(1, $events);
        $this->assertInstanceOf(BeneficiaryUpdated::class, $events[0]);
    }

    #[Test]
    public function test_change_gender_updates_gender_and_emits_event(): void
    {
        $beneficiary = Beneficiary::create(
            nationalIdentityNumber: $this->nationalIdentityNumber,
            beneficiaryType: $this->beneficiaryType,
            name: $this->name,
            nickName: null,
            birthPlace: $this->birthPlace,
            birthDate: $this->birthDate,
            gender: $this->gender,
            familyCard: $this->familyCard,
            specificAttributes: $this->childAttributes
        );

        $beneficiary->pullDomainEvents(); // Clear creation event

        $newGender = Gender::FEMALE;
        $updatedAtBefore = $beneficiary->updatedAt();

        $beneficiary->changeGender($newGender);

        $this->assertSame($newGender, $beneficiary->gender());
        $this->assertInstanceOf(DateTimeImmutable::class, $beneficiary->updatedAt());
        $this->assertGreaterThanOrEqual(
            $updatedAtBefore instanceof DateTimeImmutable ? $updatedAtBefore->getTimestamp() : 0,
            $beneficiary->updatedAt()->getTimestamp()
        );

        $events = $beneficiary->pullDomainEvents();
        $this->assertCount(1, $events);
        $this->assertInstanceOf(BeneficiaryUpdated::class, $events[0]);
    }

    #[Test]
    public function test_add_guardian_adds_to_list_and_emits_event(): void
    {
        $beneficiary = Beneficiary::create(
            nationalIdentityNumber: $this->nationalIdentityNumber,
            beneficiaryType: $this->beneficiaryType,
            name: $this->name,
            nickName: null,
            birthPlace: $this->birthPlace,
            birthDate: $this->birthDate,
            gender: $this->gender,
            familyCard: $this->familyCard,
            specificAttributes: $this->childAttributes
        );

        $beneficiary->pullDomainEvents(); // Clear creation event

        $person = new Person('Guardian Name');
        $relationship = GuardianRelationship::FATHER;

        $guardian = $beneficiary->addGuardian($person, $relationship);

        $this->assertCount(1, $beneficiary->guardians());
        $this->assertSame($guardian, $beneficiary->guardians()[0]);

        $events = $beneficiary->pullDomainEvents();
        $this->assertCount(1, $events);
        $this->assertInstanceOf(BeneficiaryUpdated::class, $events[0]);
    }

    #[Test]
    public function test_remove_guardian_removes_by_id_and_emits_event(): void
    {
        $beneficiary = Beneficiary::create(
            nationalIdentityNumber: $this->nationalIdentityNumber,
            beneficiaryType: $this->beneficiaryType,
            name: $this->name,
            nickName: null,
            birthPlace: $this->birthPlace,
            birthDate: $this->birthDate,
            gender: $this->gender,
            familyCard: $this->familyCard,
            specificAttributes: $this->childAttributes
        );

        $beneficiary->pullDomainEvents(); // Clear creation event

        $person = new Person('Guardian Name');
        $relationship = GuardianRelationship::FATHER;
        $guardian = $beneficiary->addGuardian($person, $relationship);
        $domainId = $guardian->id();
        $beneficiary->pullDomainEvents(); // Clear addGuardian event

        $beneficiary->removeGuardian($domainId);

        $this->assertEmpty($beneficiary->guardians());

        $events = $beneficiary->pullDomainEvents();

        $this->assertCount(1, $events);
        $this->assertInstanceOf(BeneficiaryUpdated::class, $events[0]);
    }

    #[Test]
    public function test_remove_all_guardians_clears_list_and_emits_event(): void
    {
        $beneficiary = Beneficiary::create(
            nationalIdentityNumber: $this->nationalIdentityNumber,
            beneficiaryType: $this->beneficiaryType,
            name: $this->name,
            nickName: null,
            birthPlace: $this->birthPlace,
            birthDate: $this->birthDate,
            gender: $this->gender,
            familyCard: $this->familyCard,
            specificAttributes: $this->childAttributes
        );

        $beneficiary->pullDomainEvents(); // Clear creation event

        $person1 = new Person('Guardian 1');
        $person2 = new Person('Guardian 2');
        $beneficiary->addGuardian($person1, GuardianRelationship::FATHER);
        $beneficiary->addGuardian($person2, GuardianRelationship::MOTHER);
        $beneficiary->pullDomainEvents(); // Clear addGuardian events

        $beneficiary->removeAllGuardians();

        $this->assertEmpty($beneficiary->guardians());

        $events = $beneficiary->pullDomainEvents();
        $this->assertCount(1, $events);
        $this->assertInstanceOf(BeneficiaryUpdated::class, $events[0]);
    }

    #[Test]
    public function test_mark_as_deleted_sets_inactive_and_emits_event(): void
    {
        $beneficiary = Beneficiary::create(
            nationalIdentityNumber: $this->nationalIdentityNumber,
            beneficiaryType: $this->beneficiaryType,
            name: $this->name,
            nickName: null,
            birthPlace: $this->birthPlace,
            birthDate: $this->birthDate,
            gender: $this->gender,
            familyCard: $this->familyCard,
            specificAttributes: $this->childAttributes
        );

        $beneficiary->pullDomainEvents(); // Clear creation event

        $beneficiary->markAsDeleted();

        $this->assertFalse($beneficiary->isActive());

        $events = $beneficiary->pullDomainEvents();
        $this->assertCount(1, $events);
        $this->assertInstanceOf(BeneficiaryDeleted::class, $events[0]);
    }

    #[Test]
    public function test_pull_domain_events_clears_events(): void
    {
        $beneficiary = Beneficiary::create(
            nationalIdentityNumber: $this->nationalIdentityNumber,
            beneficiaryType: $this->beneficiaryType,
            name: $this->name,
            nickName: null,
            birthPlace: $this->birthPlace,
            birthDate: $this->birthDate,
            gender: $this->gender,
            familyCard: $this->familyCard,
            specificAttributes: $this->childAttributes
        );

        $events1 = $beneficiary->pullDomainEvents();
        $this->assertCount(1, $events1);

        $events2 = $beneficiary->pullDomainEvents();
        $this->assertEmpty($events2);
    }

    #[Test]
    public function test_to_array_returns_correct_structure(): void
    {
        $beneficiary = Beneficiary::create(
            nationalIdentityNumber: $this->nationalIdentityNumber,
            beneficiaryType: $this->beneficiaryType,
            name: $this->name,
            nickName: null,
            birthPlace: $this->birthPlace,
            birthDate: $this->birthDate,
            gender: $this->gender,
            familyCard: $this->familyCard,
            specificAttributes: $this->childAttributes
        );

        $array = $beneficiary->toArray();

        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('nik', $array);
        $this->assertArrayHasKey('type', $array);
        $this->assertArrayHasKey('name', $array);
        $this->assertArrayHasKey('nick_name', $array);
        $this->assertArrayHasKey('birth_place', $array);
        $this->assertArrayHasKey('birth_date', $array);
        $this->assertArrayHasKey('gender', $array);
        $this->assertArrayHasKey('family_card', $array);
        $this->assertArrayHasKey('is_active', $array);
        $this->assertArrayHasKey('specific_attributes', $array);
        $this->assertArrayHasKey('guardians', $array);

        $this->assertSame('1234567890123456', $array['nik']);
        $this->assertSame('child', $array['type']);
        $this->assertIsArray($array['name']);
        $this->assertIsArray($array['family_card']);
        $this->assertIsArray($array['specific_attributes']);
        $this->assertIsArray($array['guardians']);
    }
}
