<?php

declare(strict_types=1);

namespace Tests\Unit\Entities;

use DateTimeImmutable;
use Domain\Beneficiaries\Entities\Guardian;
use Domain\Beneficiaries\ValueObjects\Enum\GuardianRelationship;
use PHPUnit\Framework\Attributes\Test;
use Shared\ValueObjects\Address;
use Shared\ValueObjects\Contact;
use Shared\ValueObjects\DomainId;
use Shared\ValueObjects\Person;
use Tests\TestCase;

final class GuardianTest extends TestCase
{
    #[Test]
    public function it_can_register_a_guardian(): void
    {
        $domainId = new DomainId('01KX6N4Z4H95PD1AMD87B6XVK9');
        $person = new Person(
            'John Doe',
            'Engineer',
            null,
            null,
            null,
        );
        $relationship = GuardianRelationship::FATHER;

        $guardian = Guardian::register($domainId, $person, $relationship);

        $this->assertSame($domainId, $guardian->beneficiaryId());
        $this->assertSame($person, $guardian->person());
        $this->assertSame($relationship, $guardian->relationship());
        $this->assertInstanceOf(DomainId::class, $guardian->id());
        $this->assertInstanceOf(DateTimeImmutable::class, $guardian->createdAt());
    }

    #[Test]
    public function it_can_reconstitute_a_guardian_from_persistence(): void
    {
        $id = new DomainId('01KX6N4Z4H95PD1AMD87B6XVK9');
        $createdAt = new DateTimeImmutable('2024-01-01 10:00:00');
        $updatedAt = new DateTimeImmutable('2024-01-01 10:30:00');
        $beneficiaryId = new DomainId('01KX6N4Z4H95PD1AMD87B6XVK9');
        $person = new Person(
            'Jane Smith',
            'Doctor',
            null,
            null,
            null,
        );
        $relationship = GuardianRelationship::MOTHER;

        $guardian = Guardian::reconstitute($id, $createdAt, $updatedAt, $beneficiaryId, $person, $relationship);

        $this->assertSame($id, $guardian->id());
        $this->assertSame($createdAt, $guardian->createdAt());
        $this->assertSame($updatedAt, $guardian->updatedAt());
        $this->assertSame($beneficiaryId, $guardian->beneficiaryId());
        $this->assertSame($person, $guardian->person());
        $this->assertSame($relationship, $guardian->relationship());
    }

    #[Test]
    public function it_can_get_guardian_properties_after_registration(): void
    {
        $domainId = new DomainId('01KX6N4Z4H95PD1AMD87B6XVK9');
        $person = new Person(
            'John Doe',
            'Engineer',
            null,
            null,
            null,
        );
        $relationship = GuardianRelationship::FATHER;

        $guardian = Guardian::register($domainId, $person, $relationship);

        $this->assertSame($domainId, $guardian->beneficiaryId());
        $this->assertSame($person, $guardian->person());
        $this->assertSame($relationship, $guardian->relationship());
    }

    #[Test]
    public function it_can_update_person(): void
    {
        $domainId = new DomainId('01KX6N4Z4H95PD1AMD87B6XVK9');
        $person = new Person(
            'John Doe',
            'Engineer',
            null,
            null,
            null,
        );
        $relationship = GuardianRelationship::FATHER;

        $guardian = Guardian::register($domainId, $person, $relationship);
        $updatedAtBefore = $guardian->updatedAt();

        $newPerson = new Person(
            'John Updated',
            'Senior Engineer',
            null,
            null,
            null,
        );
        $guardian->updatePerson($newPerson);

        $this->assertSame($newPerson, $guardian->person());
        $this->assertNotSame($updatedAtBefore, $guardian->updatedAt());
        $this->assertNotNull($guardian->updatedAt());
    }

    #[Test]
    public function it_can_change_relationship(): void
    {
        $domainId = new DomainId('01KX6N4Z4H95PD1AMD87B6XVK9');
        $person = new Person(
            'John Doe',
            'Engineer',
            null,
            null,
            null,
        );
        $relationship = GuardianRelationship::FATHER;

        $guardian = Guardian::register($domainId, $person, $relationship);
        $updatedAtBefore = $guardian->updatedAt();

        $guardian->changeRelationship(GuardianRelationship::MOTHER);

        $this->assertSame(GuardianRelationship::MOTHER, $guardian->relationship());
        $this->assertNotSame($updatedAtBefore, $guardian->updatedAt());
        $this->assertNotNull($guardian->updatedAt());
    }

    #[Test]
    public function it_can_transfer_to_different_beneficiary(): void
    {
        $beneficiaryId = new DomainId('01KX6N4Z4H95PD1AMD87B6XVK9');
        $person = new Person(
            'John Doe',
            'Engineer',
            null,
            null,
            null,
        );
        $relationship = GuardianRelationship::FATHER;

        $guardian = Guardian::register($beneficiaryId, $person, $relationship);
        $updatedAtBefore = $guardian->updatedAt();

        $newBeneficiaryId = new DomainId('01KX6N4Z4H95PD1AMD87B6XVK9');
        $guardian->transferTo($newBeneficiaryId);

        $this->assertSame($newBeneficiaryId, $guardian->beneficiaryId());
        $this->assertNotSame($updatedAtBefore, $guardian->updatedAt());
        $this->assertNotNull($guardian->updatedAt());
    }

    #[Test]
    public function it_can_serialize_to_array(): void
    {
        $domainId = new DomainId('01KX6N4Z4H95PD1AMD87B6XVK9');
        $person = new Person(
            'John Doe',
            'Engineer',
            null,
            null,
            null,
        );
        $relationship = GuardianRelationship::FATHER;

        $guardian = Guardian::register($domainId, $person, $relationship);

        $array = $guardian->toArray();

        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('beneficiary_id', $array);
        $this->assertArrayHasKey('person', $array);
        $this->assertArrayHasKey('relationship', $array);

        $this->assertSame($guardian->id()->value, $array['id']);
        $this->assertSame($guardian->beneficiaryId()->value, $array['beneficiary_id']);
        $this->assertSame($guardian->person()->toArray(), $array['person']);
        $this->assertSame($guardian->relationship()->value, $array['relationship']);
    }

    #[Test]
    public function it_is_not_equal_to_different_guardian_instance(): void
    {
        $domainId = new DomainId('01KX6N4Z4H95PD1AMD87B6XVK9');
        $person = new Person(
            'John Doe',
            'Engineer',
            null,
            null,
            null,
        );
        $relationship = GuardianRelationship::FATHER;

        $guardian1 = Guardian::register($domainId, $person, $relationship);
        $guardian2 = Guardian::register($domainId, $person, $relationship);

        $this->assertNotEquals($guardian1, $guardian2);
    }

    #[Test]
    public function it_can_generate_new_id(): void
    {
        $id = Guardian::newId();

        $this->assertInstanceOf(DomainId::class, $id);
    }

    #[Test]
    public function it_can_create_person_with_contact(): void
    {
        $address = new Address(
            'Jl. Test No. 123',
            '01',
            '01',
            'Kelurahan Test',
            'Kecamatan Test',
            'Kota Test',
            'Provinsi Test',
            '12345'
        );

        $contact = new Contact('081234567890');

        $person = new Person(
            'John Doe',
            'Engineer',
            null,
            $address,
            $contact,
        );

        $this->assertSame('John Doe', $person->name);
        $this->assertSame('Engineer', $person->occupation);
        $this->assertSame($address, $person->address);
        $this->assertSame($contact, $person->contact);
    }
}
