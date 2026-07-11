<?php

namespace Domain\Beneficiaries\Entities;

use DateTimeImmutable;
use Domain\Beneficiaries\ValueObjects\Enum\GuardianRelationship;
use Illuminate\Support\Str;
use Shared\Entities\BaseEntity;
use Shared\ValueObjects\DomainId;
use Shared\ValueObjects\Person;

/**
 * Represents a Guardian (Wali) entity within the Beneficiary aggregate.
 *
 * A Guardian is associated with exactly one Beneficiary and holds
 * personal information and relationship data.
 */
final class Guardian extends BaseEntity
{
    private DomainId $beneficiaryId;

    private Person $person;

    private GuardianRelationship $relationship;

    protected function __construct()
    {
        parent::__construct();
    }

    protected static function isValidId(string $value): bool
    {
        return Str::isUlid($value);
    }

    protected static function generateId(): DomainId
    {
        return self::createId((string) Str::ulid());
    }

    /**
     * Register a new Guardian for a Beneficiary.
     *
     * @param  DomainId  $beneficiaryId  The beneficiary this guardian belongs to
     * @param  Person  $person  Personal information (name, occupation, education, etc.)
     * @param  GuardianRelationship  $relationship  Relationship to the beneficiary
     */
    public static function register(
        DomainId $beneficiaryId,
        Person $person,
        GuardianRelationship $relationship,
    ): self {
        $entity = new self;
        $entity->beneficiaryId = $beneficiaryId;
        $entity->person = $person;
        $entity->relationship = $relationship;

        return $entity;
    }

    /**
     * Reconstitute a Guardian from persistent storage.
     */
    public static function reconstitute(
        DomainId $id,
        DateTimeImmutable $createdAt,
        ?DateTimeImmutable $updatedAt,
        DomainId $beneficiaryId,
        Person $person,
        GuardianRelationship $relationship,
    ): self {
        $entity = self::fromPersistence($id, $createdAt, $updatedAt);
        $entity->beneficiaryId = $beneficiaryId;
        $entity->person = $person;
        $entity->relationship = $relationship;

        return $entity;
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
     */
    public function updatePerson(Person $person): void
    {
        $this->person = $person;
        $this->updateTimestamp();
    }

    /**
     * Change the guardian's relationship to the beneficiary.
     */
    public function changeRelationship(GuardianRelationship $relationship): void
    {
        $this->relationship = $relationship;
        $this->updateTimestamp();
    }

    /**
     * Transfer this guardian to a different beneficiary.
     */
    public function transferTo(DomainId $beneficiaryId): void
    {
        $this->beneficiaryId = $beneficiaryId;
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
