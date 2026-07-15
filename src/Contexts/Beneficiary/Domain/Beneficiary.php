<?php

declare(strict_types=1);

namespace Copie\Contexts\Beneficiary\Domain;

use Copie\Contexts\Beneficiary\Domain\Entities\FamilyCard;
use Copie\Contexts\Beneficiary\Domain\Entities\Guardian;
use Copie\Contexts\Beneficiary\Domain\Events\BeneficiaryCreated;
use Copie\Contexts\Beneficiary\Domain\Events\BeneficiaryDeleted;
use Copie\Contexts\Beneficiary\Domain\Events\BeneficiaryUpdated;
use Copie\Contexts\Beneficiary\Domain\Exceptions\BeneficiaryAttributeException;
use Copie\Contexts\Beneficiary\Domain\ValueObjects\ChildAttributes;
use Copie\Contexts\Beneficiary\Domain\ValueObjects\Name;
use Copie\Contexts\Beneficiary\Domain\ValueObjects\NationalIdentityNumber;
use Copie\Contexts\Beneficiary\Domain\ValueObjects\SpecificAttributes;
use Copie\Shared\Domain\AggregateRoot;
use Copie\Shared\Domain\Enums\Gender;
use Copie\Shared\Domain\ValueObjects\DomainId;
use Copie\Shared\Domain\ValueObjects\Person;
use DateTimeImmutable;

/**
 * Beneficiary Aggregate Root.
 *
 * Represents a beneficiary (klien/penerima manfaat) in the system.
 * This is the only entry point for managing beneficiary data.
 */
class Beneficiary extends AggregateRoot
{
    /**
     * Maps beneficiary types to their required SpecificAttributes implementation.
     *
     * @var array<string, class-string<SpecificAttributes>>
     */
    private const TYPE_ATTRIBUTE_MAP = [
        BeneficiaryType::Child->value => ChildAttributes::class,
    ];

    private NationalIdentityNumber $nationalIdentityNumber;

    private BeneficiaryType $beneficiaryType;

    private Name $name;

    private ?string $nickName = null;

    private string $birthPlace;

    private string $birthDate;

    private Gender $gender;

    private FamilyCard $familyCard;

    private bool $isActive = true;

    private ?SpecificAttributes $specificAttributes = null;

    /** @var Guardian[] */
    private array $guardians = [];

    protected function __construct()
    {
        parent::__construct();
    }

    /**
     * Create a new Beneficiary.
     *
     * Business rules enforced:
     * 1. If type has a mapped attribute class, specificAttributes must be provided
     * 2. If type has no mapped attribute class, specificAttributes must be null
     */
    public static function create(
        NationalIdentityNumber $nationalIdentityNumber,
        BeneficiaryType $beneficiaryType,
        Name $name,
        ?string $nickName,
        string $birthPlace,
        string $birthDate,
        Gender $gender,
        FamilyCard $familyCard,
        ?SpecificAttributes $specificAttributes = null,
    ): self {
        $attributeClass = self::TYPE_ATTRIBUTE_MAP[$beneficiaryType->value] ?? null;

        if ($attributeClass !== null && ! $specificAttributes instanceof $attributeClass) {
            throw BeneficiaryAttributeException::missingAttributes($beneficiaryType);
        }

        if ($attributeClass === null && $specificAttributes instanceof SpecificAttributes) {
            throw BeneficiaryAttributeException::attributesNotAllowed($beneficiaryType);
        }

        $entity = new self;
        $entity->nationalIdentityNumber = $nationalIdentityNumber;
        $entity->beneficiaryType = $beneficiaryType;
        $entity->name = $name;
        $entity->nickName = $nickName;
        $entity->birthPlace = $birthPlace;
        $entity->birthDate = $birthDate;
        $entity->gender = $gender;
        $entity->familyCard = $familyCard;
        $entity->specificAttributes = $specificAttributes;
        $entity->updateTimestamp();

        $entity->recordDomainEvent(new BeneficiaryCreated(
            beneficiaryId: $entity->id(),
            name: $name,
            type: $beneficiaryType,
        ));

        return $entity;
    }

    /**
     * Reconstitute a Beneficiary from persistent storage.
     */
    public static function reconstitute(
        DomainId $domainId,
        DateTimeImmutable $createdAt,
        ?DateTimeImmutable $updatedAt,
        NationalIdentityNumber $nationalIdentityNumber,
        BeneficiaryType $beneficiaryType,
        Name $name,
        ?string $nickName,
        string $birthPlace,
        string $birthDate,
        Gender $gender,
        FamilyCard $familyCard,
        bool $isActive = true,
        ?SpecificAttributes $specificAttributes = null,
        array $guardians = [],
    ): self {
        $entity = self::fromPersistence($domainId, $createdAt, $updatedAt);
        $entity->nationalIdentityNumber = $nationalIdentityNumber;
        $entity->beneficiaryType = $beneficiaryType;
        $entity->name = $name;
        $entity->nickName = $nickName;
        $entity->birthPlace = $birthPlace;
        $entity->birthDate = $birthDate;
        $entity->gender = $gender;
        $entity->familyCard = $familyCard;
        $entity->isActive = $isActive;
        $entity->specificAttributes = $specificAttributes;
        $entity->guardians = $guardians;

        return $entity;
    }

    // ─── Getters ──────────────────────────────────────────────

    public function nik(): NationalIdentityNumber
    {
        return $this->nationalIdentityNumber;
    }

    public function type(): BeneficiaryType
    {
        return $this->beneficiaryType;
    }

    public function name(): Name
    {
        return $this->name;
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

    public function familyCard(): FamilyCard
    {
        return $this->familyCard;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function specificAttributes(): ?SpecificAttributes
    {
        return $this->specificAttributes;
    }

    /** @return Guardian[] */
    public function guardians(): array
    {
        return $this->guardians;
    }

    // ─── Business Methods ─────────────────────────────────────

    /**
     * Update the beneficiary's names.
     */
    public function rename(Name $name, ?string $nickName): void
    {
        $this->name = $name;
        $this->nickName = $nickName;
        $this->updateTimestamp();
        $this->recordDomainEvent(new BeneficiaryUpdated(beneficiaryId: $this->id()));
    }

    /**
     * Update birth place and date.
     */
    public function updateBirthInfo(string $birthPlace, string $birthDate): void
    {
        $this->birthPlace = $birthPlace;
        $this->birthDate = $birthDate;
        $this->updateTimestamp();
        $this->recordDomainEvent(new BeneficiaryUpdated(beneficiaryId: $this->id()));
    }

    /**
     * Change the beneficiary's gender.
     */
    public function changeGender(Gender $gender): void
    {
        $this->gender = $gender;
        $this->updateTimestamp();
        $this->recordDomainEvent(new BeneficiaryUpdated(beneficiaryId: $this->id()));
    }

    /**
     * Assign this beneficiary to a different family card.
     */
    public function assignToFamilyCard(FamilyCard $familyCard): void
    {
        $this->familyCard = $familyCard;
        $this->updateTimestamp();
        $this->recordDomainEvent(new BeneficiaryUpdated(beneficiaryId: $this->id()));
    }

    /**
     * Replace the specific attributes for this beneficiary.
     */
    public function updateSpecificAttributes(?SpecificAttributes $specificAttributes): void
    {
        $this->specificAttributes = $specificAttributes;
        $this->updateTimestamp();
        $this->recordDomainEvent(new BeneficiaryUpdated(beneficiaryId: $this->id()));
    }

    /**
     * Change the active status.
     */
    public function changeActiveStatus(bool $isActive): void
    {
        $this->isActive = $isActive;
        $this->updateTimestamp();
        $this->recordDomainEvent(new BeneficiaryUpdated(beneficiaryId: $this->id()));
    }

    /**
     * Add a guardian to this beneficiary.
     */
    public function addGuardian(Person $person, GuardianRelationship $guardianRelationship): Guardian
    {
        $guardian = Guardian::create(
            person: $person,
            beneficiaryId: $this->id(),
            relationship: $guardianRelationship,
        );

        $this->guardians[] = $guardian;
        $this->updateTimestamp();
        $this->recordDomainEvent(new BeneficiaryUpdated(beneficiaryId: $this->id()));

        return $guardian;
    }

    /**
     * Remove a guardian from this beneficiary by ID.
     */
    public function removeGuardian(DomainId $domainId): void
    {
        $this->guardians = array_values(
            array_filter(
                $this->guardians,
                fn (Guardian $guardian): bool => ! $guardian->id()->equals($domainId),
            )
        );

        $this->updateTimestamp();
        $this->recordDomainEvent(new BeneficiaryUpdated(beneficiaryId: $this->id()));
    }

    /**
     * Remove all guardians from this beneficiary.
     */
    public function removeAllGuardians(): void
    {
        $this->guardians = [];
        $this->updateTimestamp();
        $this->recordDomainEvent(new BeneficiaryUpdated(beneficiaryId: $this->id()));
    }

    /**
     * Mark beneficiary as deleted.
     */
    public function markAsDeleted(): void
    {
        $this->isActive = false;
        $this->updateTimestamp();
        $this->recordDomainEvent(new BeneficiaryDeleted(beneficiaryId: $this->id()));
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id()->value,
            'nik' => $this->nationalIdentityNumber->value,
            'type' => $this->beneficiaryType->value,
            'name' => $this->name->toArray(),
            'nick_name' => $this->nickName,
            'birth_place' => $this->birthPlace,
            'birth_date' => $this->birthDate,
            'gender' => $this->gender->value,
            'family_card' => $this->familyCard->toArray(),
            'is_active' => $this->isActive,
            'specific_attributes' => $this->specificAttributes?->toArray(),
            'guardians' => array_map(
                fn (Guardian $guardian): array => $guardian->toArray(),
                $this->guardians,
            ),
        ];
    }
}
