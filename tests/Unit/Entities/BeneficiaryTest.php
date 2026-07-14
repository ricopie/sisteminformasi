<?php

declare(strict_types=1);

namespace Tests\Unit\Entities;

use DateTimeImmutable;
use Domain\Beneficiaries\Entities\Beneficiary;
use Domain\Beneficiaries\Entities\Guardian;
use Domain\Beneficiaries\Exceptions\BeneficiaryAttributeException;
use Domain\Beneficiaries\ValueObjects\Child\ChildAttributes;
use Domain\Beneficiaries\ValueObjects\Child\Education;
use Domain\Beneficiaries\ValueObjects\Enum\BeneficiaryType;
use Domain\Beneficiaries\ValueObjects\Enum\EducationStatus;
use Domain\Beneficiaries\ValueObjects\Enum\GuardianRelationship;
use Domain\Beneficiaries\ValueObjects\Enum\NationalIdentityNumber;
use PHPUnit\Framework\Attributes\Test;
use Shared\ValueObjects\Contact;
use Shared\ValueObjects\DomainId;
use Shared\ValueObjects\Enum\EducationLevel;
use Shared\ValueObjects\Enum\Gender;
use Shared\ValueObjects\Person;
use Tests\TestCase;

final class BeneficiaryTest extends TestCase
{
    /**
     * Helper setup methods
     */
    private function makeNiks(): NationalIdentityNumber
    {
        return new NationalIdentityNumber('1234567890123456');
    }

    private function makeId(): DomainId
    {
        return new DomainId('01ARZ3NDEKTSV4RRFFQ69G5FAV');
    }

    private function makePerson(): Person
    {
        return new Person('Guardian Name', 'Job', null, null, new Contact('081234567890'));
    }

    private function makeFamilyCardId(): DomainId
    {
        return new DomainId('01ARZ3NDEKTSV4RRFFQ69G5FAV');
    }

    private function makeChildAttributes(): ChildAttributes
    {
        return new ChildAttributes(
            education: new Education(
                level: EducationLevel::ELEMENTARY,
                status: EducationStatus::CURRENTLY_ENROLLED,
                schoolName: 'SD N 1',
                grade: 5,
                nisn: '1234567890',
            ),
            hobbies: ['Reading'],
        );
    }

    /**
     * 1. Register CHILD beneficiary with ChildAttributes
     */
    #[Test]
    public function it_can_register_child_beneficiary_with_child_attributes(): void
    {
        $nationalIdentityNumber = $this->makeNiks();
        $domainId = $this->makeFamilyCardId();
        $childAttributes = $this->makeChildAttributes();

        $beneficiary = Beneficiary::register(
            $nationalIdentityNumber,
            BeneficiaryType::CHILD,
            'Full Name',
            'Nick',
            'Jakarta',
            '2010-01-01',
            Gender::MALE,
            $domainId,
            $childAttributes,
        );

        $this->assertSame($nationalIdentityNumber, $beneficiary->nik());
        $this->assertSame(BeneficiaryType::CHILD, $beneficiary->type());
        $this->assertSame('Full Name', $beneficiary->fullName());
        $this->assertSame('Nick', $beneficiary->nickName());
        $this->assertSame('Jakarta', $beneficiary->birthPlace());
        $this->assertSame('2010-01-01', $beneficiary->birthDate());
        $this->assertSame(Gender::MALE, $beneficiary->gender());
        $this->assertSame($domainId, $beneficiary->familyCardId());
        $this->assertSame($childAttributes, $beneficiary->specificAttributes());
        $this->assertInstanceOf(DomainId::class, $beneficiary->id());
        $this->assertEmpty($beneficiary->guardians());
        $this->assertInstanceOf(DateTimeImmutable::class, $beneficiary->createdAt());
    }

    /**
     * 2. Register GENERAL beneficiary without specificAttributes
     */
    #[Test]
    public function it_can_register_general_beneficiary_without_specific_attributes(): void
    {
        $nationalIdentityNumber = $this->makeNiks();
        $domainId = $this->makeFamilyCardId();

        $beneficiary = Beneficiary::register(
            $nationalIdentityNumber,
            BeneficiaryType::GENERAL,
            'Full Name',
            null,
            'Jakarta',
            '1990-01-01',
            Gender::FEMALE,
            $domainId,
        );

        $this->assertSame($nationalIdentityNumber, $beneficiary->nik());
        $this->assertSame(BeneficiaryType::GENERAL, $beneficiary->type());
        $this->assertSame('Full Name', $beneficiary->fullName());
        $this->assertNull($beneficiary->nickName());
        $this->assertSame('Jakarta', $beneficiary->birthPlace());
        $this->assertSame('1990-01-01', $beneficiary->birthDate());
        $this->assertSame(Gender::FEMALE, $beneficiary->gender());
        $this->assertSame($domainId, $beneficiary->familyCardId());
        $this->assertNull($beneficiary->specificAttributes());
        $this->assertEmpty($beneficiary->guardians());
    }

    /**
     * 3. Register CHILD without ChildAttributes throws
     */
    #[Test]
    public function it_throws_when_registering_child_without_child_attributes(): void
    {
        $nationalIdentityNumber = $this->makeNiks();
        $domainId = $this->makeFamilyCardId();

        $this->expectException(BeneficiaryAttributeException::class);
        $this->expectExceptionMessage(BeneficiaryAttributeException::missingAttributes(BeneficiaryType::CHILD)->getMessage());

        Beneficiary::register(
            $nationalIdentityNumber,
            BeneficiaryType::CHILD,
            'Name',
            null,
            'Jakarta',
            '2010-01-01',
            Gender::MALE,
            $domainId,
        );
    }

    /**
     * 4. Register GENERAL with specificAttributes throws
     */
    #[Test]
    public function it_throws_when_registering_general_with_specific_attributes(): void
    {
        $nationalIdentityNumber = $this->makeNiks();
        $domainId = $this->makeFamilyCardId();
        $childAttributes = $this->makeChildAttributes();

        $this->expectException(BeneficiaryAttributeException::class);
        $this->expectExceptionMessage(BeneficiaryAttributeException::attributesNotAllowed(BeneficiaryType::GENERAL)->getMessage());

        Beneficiary::register(
            $nationalIdentityNumber,
            BeneficiaryType::GENERAL,
            'Name',
            null,
            'Jakarta',
            '1990-01-01',
            Gender::FEMALE,
            $domainId,
            $childAttributes,
        );
    }

    /**
     * 5. Reconstitute from persistence
     */
    #[Test]
    public function it_can_reconstitute_from_persistence(): void
    {
        $domainId = $this->makeId();
        $nationalIdentityNumber = $this->makeNiks();
        $familyCardId = $this->makeFamilyCardId();
        $childAttributes = $this->makeChildAttributes();
        $guardianPerson = $this->makePerson();
        $guardian = Guardian::register(
            beneficiaryId: $domainId,
            person: $guardianPerson,
            relationship: GuardianRelationship::FATHER,
        );

        $beneficiary = Beneficiary::reconstitute(
            $domainId,
            new DateTimeImmutable('2024-01-01'),
            new DateTimeImmutable('2024-06-01'),
            $nationalIdentityNumber,
            BeneficiaryType::CHILD,
            'Full Name',
            'Nick',
            'Jakarta',
            '2010-01-01',
            Gender::MALE,
            $familyCardId,
            true,
            $childAttributes,
            $guardian,               // Guardian ...$guardians (variadic)
        );

        $this->assertSame($domainId, $beneficiary->id());
        $this->assertSame('2024-01-01', $beneficiary->createdAt()->format('Y-m-d'));
        $this->assertSame('2024-06-01', $beneficiary->updatedAt()->format('Y-m-d'));
        $this->assertSame($nationalIdentityNumber, $beneficiary->nik());
        $this->assertSame(BeneficiaryType::CHILD, $beneficiary->type());
        $this->assertSame('Full Name', $beneficiary->fullName());
        $this->assertSame('Nick', $beneficiary->nickName());
        $this->assertSame('Jakarta', $beneficiary->birthPlace());
        $this->assertSame('2010-01-01', $beneficiary->birthDate());
        $this->assertSame(Gender::MALE, $beneficiary->gender());
        $this->assertSame($familyCardId, $beneficiary->familyCardId());
        $this->assertSame($childAttributes, $beneficiary->specificAttributes());
        $this->assertCount(1, $beneficiary->guardians());
        $this->assertSame($guardian->id(), $beneficiary->guardians()[0]->id());
    }

    /**
     * 6. Reconstitute without optional params
     */
    #[Test]
    public function it_can_reconstitute_without_optional_params(): void
    {
        $domainId = $this->makeId();
        $nationalIdentityNumber = $this->makeNiks();
        $familyCardId = $this->makeFamilyCardId();

        $beneficiary = Beneficiary::reconstitute(
            $domainId,
            new DateTimeImmutable('2024-01-01'),
            null,
            $nationalIdentityNumber,
            BeneficiaryType::GENERAL,
            'Name',
            null,
            'Jakarta',
            '1990-01-01',
            Gender::FEMALE,
            $familyCardId,
        );

        $this->assertSame($domainId, $beneficiary->id());
        $this->assertSame('2024-01-01', $beneficiary->createdAt()->format('Y-m-d'));
        $this->assertNull($beneficiary->updatedAt());
        $this->assertSame($nationalIdentityNumber, $beneficiary->nik());
        $this->assertSame(BeneficiaryType::GENERAL, $beneficiary->type());
        $this->assertSame('Name', $beneficiary->fullName());
        $this->assertNull($beneficiary->nickName());
        $this->assertSame('Jakarta', $beneficiary->birthPlace());
        $this->assertSame('1990-01-01', $beneficiary->birthDate());
        $this->assertSame(Gender::FEMALE, $beneficiary->gender());
        $this->assertSame($familyCardId, $beneficiary->familyCardId());
        $this->assertNull($beneficiary->specificAttributes());
        $this->assertEmpty($beneficiary->guardians());
    }

    /**
     * 7. Rename
     */
    #[Test]
    public function it_can_rename_beneficiary(): void
    {
        $nationalIdentityNumber = $this->makeNiks();
        $domainId = $this->makeFamilyCardId();
        $childAttributes = $this->makeChildAttributes();

        $beneficiary = Beneficiary::register(
            $nationalIdentityNumber,
            BeneficiaryType::CHILD,
            'Old Name',
            'Old Nick',
            'Jakarta',
            '2010-01-01',
            Gender::MALE,
            $domainId,
            $childAttributes,
        );
        $updatedAtBefore = $beneficiary->updatedAt();

        $beneficiary->rename('New Name', 'New Nick');

        $this->assertSame('New Name', $beneficiary->fullName());
        $this->assertSame('New Nick', $beneficiary->nickName());
        $this->assertNotSame($updatedAtBefore, $beneficiary->updatedAt());
    }

    /**
     * 8. Update birth info
     */
    #[Test]
    public function it_can_update_birth_info(): void
    {
        $nationalIdentityNumber = $this->makeNiks();
        $domainId = $this->makeFamilyCardId();
        $childAttributes = $this->makeChildAttributes();

        $beneficiary = Beneficiary::register(
            $nationalIdentityNumber,
            BeneficiaryType::CHILD,
            'Name',
            null,
            'Jakarta',
            '2010-01-01',
            Gender::MALE,
            $domainId,
            $childAttributes,
        );
        $updatedAtBefore = $beneficiary->updatedAt();

        $beneficiary->updateBirthInfo('Surabaya', '2000-01-01');

        $this->assertSame('Surabaya', $beneficiary->birthPlace());
        $this->assertSame('2000-01-01', $beneficiary->birthDate());
        $this->assertNotSame($updatedAtBefore, $beneficiary->updatedAt());
    }

    /**
     * 9. Change gender
     */
    #[Test]
    public function it_can_change_gender(): void
    {
        $nationalIdentityNumber = $this->makeNiks();
        $domainId = $this->makeFamilyCardId();
        $childAttributes = $this->makeChildAttributes();

        $beneficiary = Beneficiary::register(
            $nationalIdentityNumber,
            BeneficiaryType::CHILD,
            'Name',
            null,
            'Jakarta',
            '2010-01-01',
            Gender::MALE,
            $domainId,
            $childAttributes,
        );
        $updatedAtBefore = $beneficiary->updatedAt();

        $beneficiary->changeGender(Gender::FEMALE);

        $this->assertSame(Gender::FEMALE, $beneficiary->gender());
        $this->assertNotSame($updatedAtBefore, $beneficiary->updatedAt());
    }

    /**
     * 10. Update specific attributes
     */
    #[Test]
    public function it_can_update_specific_attributes(): void
    {
        $nationalIdentityNumber = $this->makeNiks();
        $domainId = $this->makeFamilyCardId();

        $beneficiary = Beneficiary::register(
            $nationalIdentityNumber,
            BeneficiaryType::GENERAL,
            'Name',
            null,
            'Jakarta',
            '1990-01-01',
            Gender::FEMALE,
            $domainId,
        );
        $updatedAtBefore = $beneficiary->updatedAt();

        $beneficiary->updateSpecificAttributes(null);

        $this->assertNull($beneficiary->specificAttributes());
        $this->assertNotSame($updatedAtBefore, $beneficiary->updatedAt());

        $childAttributes = $this->makeChildAttributes();
        $beneficiary->updateSpecificAttributes($childAttributes);

        $this->assertSame($childAttributes, $beneficiary->specificAttributes());
        $this->assertNotSame($updatedAtBefore, $beneficiary->updatedAt());
    }

    /**
     * 11. Add guardian
     */
    #[Test]
    public function it_can_add_guardian(): void
    {
        $nationalIdentityNumber = $this->makeNiks();
        $domainId = $this->makeFamilyCardId();

        $beneficiary = Beneficiary::register(
            $nationalIdentityNumber,
            BeneficiaryType::GENERAL,
            'Name',
            null,
            'Jakarta',
            '1990-01-01',
            Gender::FEMALE,
            $domainId,
        );
        $updatedAtBefore = $beneficiary->updatedAt();

        $person = $this->makePerson();
        $guardian = $beneficiary->addGuardian($person, GuardianRelationship::FATHER);

        $this->assertCount(1, $beneficiary->guardians());
        $this->assertSame($guardian, $beneficiary->guardians()[0]);
        $this->assertSame($person, $guardian->person());
        $this->assertSame(GuardianRelationship::FATHER, $guardian->relationship());
        $this->assertSame($beneficiary->id(), $guardian->beneficiaryId());
        $this->assertNotSame($updatedAtBefore, $beneficiary->updatedAt());
    }

    /**
     * 12. Remove guardian
     */
    #[Test]
    public function it_can_remove_guardian(): void
    {
        $nationalIdentityNumber = $this->makeNiks();
        $domainId = $this->makeFamilyCardId();

        $beneficiary = Beneficiary::register(
            $nationalIdentityNumber,
            BeneficiaryType::GENERAL,
            'Name',
            null,
            'Jakarta',
            '1990-01-01',
            Gender::FEMALE,
            $domainId,
        );
        $person = $this->makePerson();
        $guardian = $beneficiary->addGuardian($person, GuardianRelationship::FATHER);
        $updatedAtBefore = $beneficiary->updatedAt();

        $beneficiary->removeGuardian($guardian->id()->value);

        $this->assertEmpty($beneficiary->guardians());
        $this->assertNotSame($updatedAtBefore, $beneficiary->updatedAt());
    }

    /**
     * 13. Remove all guardians
     */
    #[Test]
    public function it_can_remove_all_guardians(): void
    {
        $nationalIdentityNumber = $this->makeNiks();
        $domainId = $this->makeFamilyCardId();

        $beneficiary = Beneficiary::register(
            $nationalIdentityNumber,
            BeneficiaryType::GENERAL,
            'Name',
            null,
            'Jakarta',
            '1990-01-01',
            Gender::FEMALE,
            $domainId,
        );
        $person1 = $this->makePerson();
        $person = new Person('Guardian2', 'Job2', null, null, new Contact('081234567891'));
        $beneficiary->addGuardian($person1, GuardianRelationship::FATHER);
        $beneficiary->addGuardian($person, GuardianRelationship::MOTHER);

        $updatedAtBefore = $beneficiary->updatedAt();

        $beneficiary->removeAllGuardians();

        $this->assertEmpty($beneficiary->guardians());
        $this->assertNotSame($updatedAtBefore, $beneficiary->updatedAt());
    }

    /**
     * 14. Remove non-existent guardian (no error)
     */
    #[Test]
    public function it_can_remove_non_existent_guardian(): void
    {
        $nationalIdentityNumber = $this->makeNiks();
        $domainId = $this->makeFamilyCardId();

        $beneficiary = Beneficiary::register(
            $nationalIdentityNumber,
            BeneficiaryType::GENERAL,
            'Name',
            null,
            'Jakarta',
            '1990-01-01',
            Gender::FEMALE,
            $domainId,
        );

        $beneficiary->removeGuardian('nonexistent-id');

        $this->assertEmpty($beneficiary->guardians());
    }

    /**
     * 15. Restore family card ID
     */
    #[Test]
    public function it_can_restore_family_card_id(): void
    {
        $nationalIdentityNumber = $this->makeNiks();
        $familyCardId = $this->makeFamilyCardId();
        $domainId = new DomainId('01ARZ3NDEKTSV4RRFFQ69G5FAV2');

        $beneficiary = Beneficiary::register(
            $nationalIdentityNumber,
            BeneficiaryType::CHILD,
            'Name',
            null,
            'Jakarta',
            '2010-01-01',
            Gender::MALE,
            $familyCardId,
            $this->makeChildAttributes(),
        );
        $updatedAtBefore = $beneficiary->updatedAt();

        $beneficiary->restoreFamilyCardId($domainId);

        $this->assertSame($domainId, $beneficiary->familyCardId());
        $this->assertSame($updatedAtBefore, $beneficiary->updatedAt());
    }

    /**
     * 16. Assign to family card
     */
    #[Test]
    public function it_can_assign_to_family_card(): void
    {
        $nationalIdentityNumber = $this->makeNiks();
        $familyCardId = $this->makeFamilyCardId();
        $domainId = new DomainId('01ARZ3NDEKTSV4RRFFQ69G5FAV2');

        $beneficiary = Beneficiary::register(
            $nationalIdentityNumber,
            BeneficiaryType::CHILD,
            'Name',
            null,
            'Jakarta',
            '2010-01-01',
            Gender::MALE,
            $familyCardId,
            $this->makeChildAttributes(),
        );
        $updatedAtBefore = $beneficiary->updatedAt();

        $beneficiary->assignToFamilyCard($domainId);

        $this->assertSame($domainId, $beneficiary->familyCardId());
        $this->assertNotSame($updatedAtBefore, $beneficiary->updatedAt());
    }

    /**
     * 17. Restore guardians (for persistence)
     */
    #[Test]
    public function it_can_restore_guardians(): void
    {
        $nationalIdentityNumber = $this->makeNiks();
        $domainId = $this->makeFamilyCardId();
        $this->makeId();

        $beneficiary = Beneficiary::register(
            $nationalIdentityNumber,
            BeneficiaryType::CHILD,
            'Name',
            null,
            'Jakarta',
            '2010-01-01',
            Gender::MALE,
            $domainId,
            $this->makeChildAttributes(),
        );
        $person1 = $this->makePerson();
        $person = new Person('Guardian2', 'Job2', null, null, new Contact('081234567891'));
        $guardian1 = $beneficiary->addGuardian($person1, GuardianRelationship::FATHER);
        $guardian2 = $beneficiary->addGuardian($person, GuardianRelationship::MOTHER);
        $beneficiary->removeAllGuardians();

        $beneficiary->restoreGuardians($guardian1, $guardian2);

        $this->assertCount(2, $beneficiary->guardians());
        $this->assertSame($guardian1->id(), $beneficiary->guardians()[0]->id());
        $this->assertSame($guardian2->id(), $beneficiary->guardians()[1]->id());
    }

    /**
     * 18. To array serialization
     */
    #[Test]
    public function it_can_serialize_to_array(): void
    {
        $nationalIdentityNumber = $this->makeNiks();
        $domainId = $this->makeFamilyCardId();
        $childAttributes = $this->makeChildAttributes();

        $beneficiary = Beneficiary::register(
            $nationalIdentityNumber,
            BeneficiaryType::CHILD,
            'Full Name',
            'Nick',
            'Jakarta',
            '2010-01-01',
            Gender::MALE,
            $domainId,
            $childAttributes,
        );
        $person = $this->makePerson();
        $guardian = $beneficiary->addGuardian($person, GuardianRelationship::FATHER);

        $array = $beneficiary->toArray();

        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('nik', $array);
        $this->assertArrayHasKey('type', $array);
        $this->assertArrayHasKey('full_name', $array);
        $this->assertArrayHasKey('nick_name', $array);
        $this->assertArrayHasKey('birth_place', $array);
        $this->assertArrayHasKey('birth_date', $array);
        $this->assertArrayHasKey('gender', $array);
        $this->assertArrayHasKey('family_card_id', $array);
        $this->assertArrayHasKey('specific_attributes', $array);
        $this->assertArrayHasKey('guardians', $array);

        $this->assertSame($beneficiary->id()->value, $array['id']);
        $this->assertSame($nationalIdentityNumber->value, $array['nik']);
        $this->assertSame(BeneficiaryType::CHILD->value, $array['type']);
        $this->assertSame('Full Name', $array['full_name']);
        $this->assertSame('Nick', $array['nick_name']);
        $this->assertSame('Jakarta', $array['birth_place']);
        $this->assertSame('2010-01-01', $array['birth_date']);
        $this->assertSame(Gender::MALE->value, $array['gender']);
        $this->assertSame($domainId->value, $array['family_card_id']);
        $this->assertSame($childAttributes->toArray(), $array['specific_attributes']);
        $this->assertCount(1, $array['guardians']);
        $this->assertSame($guardian->id()->value, $array['guardians'][0]['id']);
    }

    /**
     * 19. Entity equality
     */
    #[Test]
    public function it_is_not_equal_to_different_beneficiary_instance(): void
    {
        $nationalIdentityNumber = $this->makeNiks();
        $domainId = $this->makeFamilyCardId();
        $childAttributes = $this->makeChildAttributes();

        $beneficiary1 = Beneficiary::register(
            $nationalIdentityNumber,
            BeneficiaryType::CHILD,
            'Name',
            null,
            'Jakarta',
            '2010-01-01',
            Gender::MALE,
            $domainId,
            $childAttributes,
        );
        $beneficiary2 = Beneficiary::register(
            $nationalIdentityNumber,
            BeneficiaryType::CHILD,
            'Name',
            null,
            'Jakarta',
            '2010-01-01',
            Gender::MALE,
            $domainId,
            $childAttributes,
        );

        $this->assertNotEquals($beneficiary1, $beneficiary2);
    }

    /**
     * 20. Generate new ID
     */
    #[Test]
    public function it_can_generate_new_id(): void
    {
        $id = Beneficiary::newId();

        $this->assertInstanceOf(DomainId::class, $id);
    }

    /**
     * 21. Guardian toArray preserves nested structure
     */
    #[Test]
    public function it_guardian_to_array_preserves_nested_structure(): void
    {
        $nationalIdentityNumber = $this->makeNiks();
        $domainId = $this->makeFamilyCardId();
        $childAttributes = $this->makeChildAttributes();

        $beneficiary = Beneficiary::register(
            $nationalIdentityNumber,
            BeneficiaryType::CHILD,
            'Name',
            null,
            'Jakarta',
            '2010-01-01',
            Gender::MALE,
            $domainId,
            $childAttributes,
        );
        $person = $this->makePerson();
        $beneficiary->addGuardian($person, GuardianRelationship::FATHER);

        $array = $beneficiary->toArray();

        $this->assertArrayHasKey('person', $array['guardians'][0]);
        $this->assertArrayHasKey('contact', $array['guardians'][0]['person']);
        $this->assertSame($person->contact->phone, $array['guardians'][0]['person']['contact']['phone']);
    }
}
