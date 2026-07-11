<?php

namespace Domain\Beneficiaries\Entities;

use DateTimeImmutable;
use Domain\Beneficiaries\Exceptions\BeneficiaryAttributeException;
use Domain\Beneficiaries\ValueObjects\Child\ChildAttributes;
use Domain\Beneficiaries\ValueObjects\Enum\BeneficiaryType;
use Domain\Beneficiaries\ValueObjects\Enum\GuardianRelationship;
use Domain\Beneficiaries\ValueObjects\Enum\NationalIdentityNumber;
use Domain\Beneficiaries\ValueObjects\SpecificAttributes;
use Illuminate\Support\Str;
use Shared\Entities\BaseEntity;
use Shared\ValueObjects\DomainId;
use Shared\ValueObjects\Enum\Gender;
use Shared\ValueObjects\Person;

final class Beneficiary extends BaseEntity
{
    /** @var array<string, class-string<SpecificAttributes>> */
    private const TYPE_ATTRIBUTE_MAP = [
        BeneficiaryType::CHILD->value => ChildAttributes::class,
    ];

    private NationalIdentityNumber $nik;

    private BeneficiaryType $type;

    private string $fullName;

    private ?string $nickName = null;

    private string $birthPlace;

    private string $birthDate;

    private Gender $gender;

    private DomainId $familyCardId;

    private ?SpecificAttributes $specificAttributes = null;

    /** @var Guardian[] */
    private array $guardians = [];

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
     * Register a new Beneficiary.
     *
     * @param  NationalIdentityNumber  $nik  NIK 16 digits
     * @param  BeneficiaryType  $type  Beneficiary type
     * @param  string  $fullName  Full name
     * @param  string|null  $nickName  Nickname
     * @param  string  $birthPlace  Place of birth
     * @param  string  $birthDate  Date of birth (Y-m-d)
     * @param  Gender  $gender  Gender
     * @param  DomainId  $familyCardId  Family Card ID
     * @param  SpecificAttributes|null  $specificAttributes  Type-specific attributes
     */
    public static function register(
        NationalIdentityNumber $nik,
        BeneficiaryType $type,
        string $fullName,
        ?string $nickName,
        string $birthPlace,
        string $birthDate,
        Gender $gender,
        DomainId $familyCardId,
        ?SpecificAttributes $specificAttributes = null,
    ): self {
        $attributeClass = self::TYPE_ATTRIBUTE_MAP[$type->value] ?? null;

        if ($attributeClass !== null && ! $specificAttributes instanceof $attributeClass) {
            throw BeneficiaryAttributeException::missingAttributes($type);
        }

        if ($attributeClass === null && $specificAttributes instanceof SpecificAttributes) {
            throw BeneficiaryAttributeException::attributesNotAllowed($type);
        }

        $entity = new self;
        $entity->nik = $nik;
        $entity->type = $type;
        $entity->fullName = $fullName;
        $entity->nickName = $nickName;
        $entity->birthPlace = $birthPlace;
        $entity->birthDate = $birthDate;
        $entity->gender = $gender;
        $entity->familyCardId = $familyCardId;
        $entity->specificAttributes = $specificAttributes;
        $entity->updateTimestamp();

        return $entity;
    }

    /**
     * Reconstitute a Beneficiary from persistent storage.
     *
     * This is the sole entry point for infrastructure layer (repositories)
     * to rebuild a fully-hydrated domain entity with all its properties,
     * identity, and timestamps exactly as persisted.
     *
     * @param  DomainId  $id  Persisted entity identity
     * @param  DateTimeImmutable  $createdAt  Original creation timestamp
     * @param  DateTimeImmutable|null  $updatedAt  Optional last update timestamp
     * @param  NationalIdentityNumber  $nik  16-digit NIK
     * @param  BeneficiaryType  $type  Beneficiary type
     * @param  string  $fullName  Full name
     * @param  string|null  $nickName  Nickname
     * @param  string  $birthPlace  Place of birth
     * @param  string  $birthDate  Date of birth
     * @param  Gender  $gender  Gender
     * @param  DomainId  $familyCardId  Associated FamilyCard ID
     * @param  SpecificAttributes|null  $specificAttributes  Type-specific attributes
     * @param  Guardian  ...$guardians  Associated guardians
     */
    public static function reconstitute(
        DomainId $id,
        DateTimeImmutable $createdAt,
        ?DateTimeImmutable $updatedAt,
        NationalIdentityNumber $nik,
        BeneficiaryType $type,
        string $fullName,
        ?string $nickName,
        string $birthPlace,
        string $birthDate,
        Gender $gender,
        DomainId $familyCardId,
        ?SpecificAttributes $specificAttributes = null,
        Guardian ...$guardians,
    ): self {
        $entity = self::fromPersistence($id, $createdAt, $updatedAt);
        $entity->nik = $nik;
        $entity->type = $type;
        $entity->fullName = $fullName;
        $entity->nickName = $nickName;
        $entity->birthPlace = $birthPlace;
        $entity->birthDate = $birthDate;
        $entity->gender = $gender;
        $entity->familyCardId = $familyCardId;
        $entity->specificAttributes = $specificAttributes;
        $entity->guardians = $guardians;

        return $entity;
    }

    /**
     * Restore the family card identity (for persistence reconstitution).
     */
    public function restoreFamilyCardId(DomainId $familyCardId): void
    {
        $this->familyCardId = $familyCardId;
    }

    /**
     * Assign this beneficiary to a different family card.
     */
    public function assignToFamilyCard(DomainId $familyCardId): void
    {
        $this->familyCardId = $familyCardId;
        $this->updateTimestamp();
    }

    /**
     * Restore guardians from persistence (for reconstitution only).
     */
    public function restoreGuardians(Guardian ...$guardians): void
    {
        $this->guardians = $guardians;
    }

    /**
     * Add a guardian to this beneficiary.
     *
     * Creates a new Guardian entity linked to this beneficiary
     * and appends it to the internal collection.
     *
     * @return Guardian The newly created guardian
     */
    public function addGuardian(Person $person, GuardianRelationship $relationship): Guardian
    {
        $guardian = Guardian::register(
            beneficiaryId: $this->id(),
            person: $person,
            relationship: $relationship,
        );

        $this->guardians[] = $guardian;
        $this->updateTimestamp();

        return $guardian;
    }

    /**
     * Remove a guardian from this beneficiary by ID.
     */
    public function removeGuardian(string $guardianId): void
    {
        $this->guardians = array_values(
            array_filter(
                $this->guardians,
                fn (Guardian $guardian): bool => $guardian->id()->value !== $guardianId,
            )
        );

        $this->updateTimestamp();
    }

    public function nik(): NationalIdentityNumber
    {
        return $this->nik;
    }

    public function type(): BeneficiaryType
    {
        return $this->type;
    }

    public function fullName(): string
    {
        return $this->fullName;
    }

    public function nickName(): ?string
    {
        return $this->nickName;
    }

    public function birthPlace(): string
    {
        return $this->birthPlace;
    }

    public function birthDate(): string
    {
        return $this->birthDate;
    }

    public function gender(): Gender
    {
        return $this->gender;
    }

    public function familyCardId(): DomainId
    {
        return $this->familyCardId;
    }

    public function specificAttributes(): ?SpecificAttributes
    {
        return $this->specificAttributes;
    }

    /**
     * @return Guardian[]
     */
    public function guardians(): array
    {
        return $this->guardians;
    }

    /**
     * Update the beneficiary's names.
     */
    public function rename(string $fullName, ?string $nickName): void
    {
        $this->fullName = $fullName;
        $this->nickName = $nickName;
        $this->updateTimestamp();
    }

    /**
     * Update birth place and date.
     */
    public function updateBirthInfo(string $birthPlace, string $birthDate): void
    {
        $this->birthPlace = $birthPlace;
        $this->birthDate = $birthDate;
        $this->updateTimestamp();
    }

    /**
     * Change the beneficiary's gender.
     */
    public function changeGender(Gender $gender): void
    {
        $this->gender = $gender;
        $this->updateTimestamp();
    }

    /**
     * Replace the specific attributes for this beneficiary.
     */
    public function updateSpecificAttributes(?SpecificAttributes $specificAttributes): void
    {
        $this->specificAttributes = $specificAttributes;
        $this->updateTimestamp();
    }

    /**
     * Remove all guardians from this beneficiary.
     */
    public function removeAllGuardians(): void
    {
        $this->guardians = [];
        $this->updateTimestamp();
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id()->value,
            'nik' => $this->nik->value,
            'type' => $this->type->value,
            'full_name' => $this->fullName,
            'nick_name' => $this->nickName,
            'birth_place' => $this->birthPlace,
            'birth_date' => $this->birthDate,
            'gender' => $this->gender->value,
            'family_card_id' => $this->familyCardId->value,
            'specific_attributes' => $this->specificAttributes?->toArray(),
            'guardians' => array_map(
                fn (Guardian $guardian): array => $guardian->toArray(),
                $this->guardians,
            ),
        ];
    }
}
