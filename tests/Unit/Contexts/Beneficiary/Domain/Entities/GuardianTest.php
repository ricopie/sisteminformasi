<?php

declare(strict_types=1);

namespace Tests\Unit\Contexts\Beneficiary\Domain\Entities;

use Copie\Contexts\Beneficiary\Domain\Entities\Guardian;
use Copie\Contexts\Beneficiary\Domain\Enums\GuardianRelationship;
use Copie\Shared\Domain\ValueObjects\DomainId;
use Copie\Shared\Domain\ValueObjects\Person;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class GuardianTest extends TestCase
{
    #[Test]
    public function test_create_generates_id_and_timestamp(): void
    {
        $domainId = DomainId::generate();
        $person = new Person('Guardian Name');
        $relationship = GuardianRelationship::FATHER;

        $guardian = Guardian::create($domainId, $person, $relationship);

        $this->assertInstanceOf(DomainId::class, $guardian->id());
        $this->assertInstanceOf(DateTimeImmutable::class, $guardian->createdAt());
        $this->assertNotInstanceOf(DateTimeImmutable::class, $guardian->updatedAt());
    }

    #[Test]
    public function test_reconstitute_restores_from_persistence(): void
    {
        $domainId = DomainId::generate();
        $beneficiaryId = DomainId::generate();
        $createdAt = new DateTimeImmutable('2024-01-01');
        $updatedAt = new DateTimeImmutable('2024-06-01');
        $person = new Person('Guardian Name');
        $relationship = GuardianRelationship::MOTHER;

        $guardian = Guardian::reconstitute(
            $domainId,
            $createdAt,
            $updatedAt,
            $beneficiaryId,
            $person,
            $relationship
        );

        $this->assertTrue($guardian->id()->equals($domainId));
        $this->assertTrue($guardian->beneficiaryId()->equals($beneficiaryId));
        $this->assertEquals($createdAt, $guardian->createdAt());
        $this->assertEquals($updatedAt, $guardian->updatedAt());
    }

    #[Test]
    public function test_beneficiary_id_returns_correct_id(): void
    {
        $domainId = DomainId::generate();
        $person = new Person('Guardian Name');
        $relationship = GuardianRelationship::FATHER;

        $guardian = Guardian::create($domainId, $person, $relationship);

        $this->assertTrue($guardian->beneficiaryId()->equals($domainId));
    }

    #[Test]
    public function test_person_returns_the_person(): void
    {
        $domainId = DomainId::generate();
        $person = new Person('Guardian Name');
        $relationship = GuardianRelationship::FATHER;

        $guardian = Guardian::create($domainId, $person, $relationship);

        $this->assertSame($person, $guardian->person());
    }

    #[Test]
    public function test_relationship_returns_the_relationship(): void
    {
        $domainId = DomainId::generate();
        $person = new Person('Guardian Name');
        $relationship = GuardianRelationship::FATHER;

        $guardian = Guardian::create($domainId, $person, $relationship);

        $this->assertSame($relationship, $guardian->relationship());
    }

    #[Test]
    public function test_update_person_changes_person_and_timestamp(): void
    {
        $domainId = DomainId::generate();
        $person = new Person('Guardian Name');
        $relationship = GuardianRelationship::FATHER;

        $guardian = Guardian::create($domainId, $person, $relationship);
        $updatedAtBefore = $guardian->updatedAt();

        $newPerson = new Person('New Guardian Name');
        $guardian->updatePerson($newPerson);

        $this->assertSame($newPerson, $guardian->person());
        $this->assertInstanceOf(DateTimeImmutable::class, $guardian->updatedAt());
        $this->assertGreaterThanOrEqual(
            $updatedAtBefore instanceof DateTimeImmutable ? $updatedAtBefore->getTimestamp() : 0,
            $guardian->updatedAt()->getTimestamp()
        );
    }

    #[Test]
    public function test_change_relationship_changes_relationship_and_timestamp(): void
    {
        $domainId = DomainId::generate();
        $person = new Person('Guardian Name');
        $relationship = GuardianRelationship::FATHER;

        $guardian = Guardian::create($domainId, $person, $relationship);
        $updatedAtBefore = $guardian->updatedAt();

        $newRelationship = GuardianRelationship::MOTHER;
        $guardian->changeRelationship($newRelationship);

        $this->assertSame($newRelationship, $guardian->relationship());
        $this->assertInstanceOf(DateTimeImmutable::class, $guardian->updatedAt());
        $this->assertGreaterThanOrEqual(
            $updatedAtBefore instanceof DateTimeImmutable ? $updatedAtBefore->getTimestamp() : 0,
            $guardian->updatedAt()->getTimestamp()
        );
    }

    #[Test]
    public function test_transfer_to_changes_beneficiary_id_and_timestamp(): void
    {
        $domainId = DomainId::generate();
        $person = new Person('Guardian Name');
        $relationship = GuardianRelationship::FATHER;

        $guardian = Guardian::create($domainId, $person, $relationship);
        $updatedAtBefore = $guardian->updatedAt();

        $newDomainId = DomainId::generate();
        $guardian->transferTo($newDomainId);

        $this->assertTrue($guardian->beneficiaryId()->equals($newDomainId));
        $this->assertInstanceOf(DateTimeImmutable::class, $guardian->updatedAt());
        $this->assertGreaterThanOrEqual(
            $updatedAtBefore instanceof DateTimeImmutable ? $updatedAtBefore->getTimestamp() : 0,
            $guardian->updatedAt()->getTimestamp()
        );
    }

    #[Test]
    public function test_to_array_returns_correct_structure(): void
    {
        $domainId = DomainId::generate();
        $person = new Person('Guardian Name');
        $relationship = GuardianRelationship::FATHER;

        $guardian = Guardian::create($domainId, $person, $relationship);
        $array = $guardian->toArray();

        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('beneficiary_id', $array);
        $this->assertArrayHasKey('person', $array);
        $this->assertArrayHasKey('relationship', $array);

        $this->assertSame('father', $array['relationship']);
        $this->assertIsArray($array['person']);
    }

    #[Test]
    public function test_equals_returns_true_for_same_id_and_class(): void
    {
        $domainId = DomainId::generate();
        $beneficiaryId = DomainId::generate();
        $createdAt = new DateTimeImmutable('2024-01-01');
        $updatedAt = new DateTimeImmutable('2024-06-01');
        $person = new Person('Guardian Name');
        $relationship = GuardianRelationship::FATHER;

        $guardian1 = Guardian::reconstitute(
            $domainId,
            $createdAt,
            $updatedAt,
            $beneficiaryId,
            $person,
            $relationship
        );

        $guardian2 = Guardian::reconstitute(
            $domainId,
            $createdAt,
            $updatedAt,
            $beneficiaryId,
            $person,
            $relationship
        );

        $this->assertTrue($guardian1->equals($guardian2));
    }

    #[Test]
    public function test_equals_returns_false_for_different_ids(): void
    {
        $domainId1 = DomainId::generate();
        $domainId2 = DomainId::generate();
        $domainId = DomainId::generate();
        $createdAt = new DateTimeImmutable('2024-01-01');
        $updatedAt = new DateTimeImmutable('2024-06-01');
        $person = new Person('Guardian Name');
        $relationship = GuardianRelationship::FATHER;

        $guardian1 = Guardian::reconstitute(
            $domainId1,
            $createdAt,
            $updatedAt,
            $domainId,
            $person,
            $relationship
        );

        $guardian2 = Guardian::reconstitute(
            $domainId2,
            $createdAt,
            $updatedAt,
            $domainId,
            $person,
            $relationship
        );

        $this->assertFalse($guardian1->equals($guardian2));
    }
}
