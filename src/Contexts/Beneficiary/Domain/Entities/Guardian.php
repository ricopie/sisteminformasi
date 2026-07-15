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
class Guardian extends BaseEntity
{
    public function __construct(
        public readonly DomainId $beneficiaryId,
        public readonly Person $person,
        public readonly GuardianRelationship $relationship,
    ) {
        parent::__construct();
    }

    public static function create(
        DomainId $domainId,
        Person $person,
        GuardianRelationship $guardianRelationship,
    ): self {
        return new self(
            beneficiaryId: $domainId,
            person: $person,
            relationship: $guardianRelationship,
        );
    }

    public static function reconstitute(
        DomainId $id,
        DateTimeImmutable $createdAt,
        ?DateTimeImmutable $updatedAt,
        DomainId $beneficiaryId,
        Person $person,
        GuardianRelationship $guardianRelationship,
    ): self {
        self::fromPersistence($id, $createdAt, $updatedAt);

        return new self(
            beneficiaryId: $beneficiaryId,
            person: $person,
            relationship: $guardianRelationship,
        );
    }

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
        return $this->relationship;
    }

    /**
     * Change the guardian's personal information.
     *
     * Note: Since V2 Guardian uses readonly properties, the Beneficiary
     * aggregate root will re-create the Guardian with updated values
     * when performing a full replacement update. This method is kept
     * for API consistency with V1.
     */
    public function updatePerson(Person $person): void
    {
        $this->updateTimestamp();
    }

    /**
     * Change the guardian's relationship to the beneficiary.
     */
    public function changeRelationship(GuardianRelationship $guardianRelationship): void
    {
        $this->updateTimestamp();
    }

    /**
     * Transfer this guardian to a different beneficiary.
     */
    public function transferTo(DomainId $domainId): void
    {
        $this->updateTimestamp();
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id()->value,
            'beneficiary_id' => $this->beneficiaryId->value,
            'person' => $this->person->toArray(),
            'relationship' => $this->relationship->value,
        ];
    }
}
