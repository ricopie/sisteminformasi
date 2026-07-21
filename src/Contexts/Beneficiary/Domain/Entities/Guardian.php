<?php

declare(strict_types=1);

namespace Copie\Contexts\Beneficiary\Domain\Entities;

use Copie\Contexts\Beneficiary\Domain\Enums\GuardianRelationship;
use Copie\Shared\Domain\BaseEntity;
use Copie\Shared\Domain\ValueObjects\DomainId;
use Copie\Shared\Domain\ValueObjects\Person;
use DateTimeImmutable;

/**
 * Guardian entity — internal entity within Beneficiary Aggregate.
 *
 * Represents a guardian (wali) responsible for a beneficiary.
 * Managed by Beneficiary aggregate root.
 */
final class Guardian extends BaseEntity
{
    private DomainId $beneficiaryId;

    private Person $person;

    private GuardianRelationship $guardianRelationship;

    protected function __construct()
    {
        parent::__construct();
    }

    /** Create a new Guardian. */
    public static function create(
        DomainId $domainId,
        Person $person,
        GuardianRelationship $guardianRelationship,
    ): self {
        $entity = new self();
        $entity->beneficiaryId = $domainId;
        $entity->person = $person;
        $entity->guardianRelationship = $guardianRelationship;

        return $entity;
    }

    /** Reconstitute a Guardian from persistent storage. */
    public static function reconstitute(
        DomainId $id,
        DateTimeImmutable $createdAt,
        ?DateTimeImmutable $updatedAt,
        DomainId $beneficiaryId,
        Person $person,
        GuardianRelationship $guardianRelationship,
    ): self {
        $guardian = self::fromPersistence($id, $createdAt, $updatedAt);
        $guardian->beneficiaryId = $beneficiaryId;
        $guardian->person = $person;
        $guardian->guardianRelationship = $guardianRelationship;

        return $guardian;
    }

    // ─── Getters ──────────────────────────────────────────────

    public function beneficiaryId(): DomainId
    {
        return $this->beneficiaryId;
    }

    public function person(): Person
    {
        return $this->person;
    }

    public function relationship(): GuardianRelationship
    {
        return $this->guardianRelationship;
    }

    // ─── Business Methods ─────────────────────────────────────

    /** Update the guardian's personal information. */
    public function updatePerson(Person $person): void
    {
        $this->person = $person;
        $this->updateTimestamp();
    }

    /** Change the relationship type. */
    public function changeRelationship(GuardianRelationship $guardianRelationship): void
    {
        $this->guardianRelationship = $guardianRelationship;
        $this->updateTimestamp();
    }

    /** Transfer this guardian to a different beneficiary. */
    public function transferTo(DomainId $domainId): void
    {
        $this->beneficiaryId = $domainId;
        $this->updateTimestamp();
    }

    /** Serialize to array. */
    public function toArray(): array
    {
        return [
            'id' => $this->id()->value,
            'beneficiary_id' => $this->beneficiaryId->value,
            'person' => $this->person->toArray(),
            'relationship' => $this->guardianRelationship->value,
        ];
    }
}
