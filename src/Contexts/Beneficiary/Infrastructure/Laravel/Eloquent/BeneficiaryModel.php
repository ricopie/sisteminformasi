<?php

declare(strict_types=1);

namespace Copie\Contexts\Beneficiary\Infrastructure\Laravel\Eloquent;

use Copie\Contexts\Beneficiary\Domain\Beneficiary;
use Copie\Contexts\Beneficiary\Domain\BeneficiaryType;
use Copie\Contexts\Beneficiary\Domain\Entities\FamilyCard;
use Copie\Contexts\Beneficiary\Domain\Entities\Guardian;
use Copie\Contexts\Beneficiary\Domain\Enums\GuardianRelationship;
use Copie\Contexts\Beneficiary\Domain\ValueObjects\ChildAttributes;
use Copie\Contexts\Beneficiary\Domain\ValueObjects\Name;
use Copie\Contexts\Beneficiary\Domain\ValueObjects\NationalIdentityNumber;
use Copie\Contexts\Beneficiary\Domain\ValueObjects\SpecificAttributes;
use Copie\Contexts\Beneficiary\Infrastructure\Laravel\Casts\BeneficiaryAttributesCast;
use Copie\Shared\Domain\Enums\Gender;
use Copie\Shared\Domain\ValueObjects\Address;
use Copie\Shared\Domain\ValueObjects\DomainId;
use Copie\Shared\Domain\ValueObjects\Person;
use DateTimeImmutable;
use Illuminate\Database\Eloquent\Model;
use Spatie\LaravelCipherSweet\Concerns\UsesCipherSweet;
use Spatie\LaravelCipherSweet\Contracts\CipherSweetEncrypted;

/**
 * Eloquent model for Beneficiary aggregate.
 *
 * This model belongs to the Infrastructure layer and is responsible
 * for mapping the Beneficiary aggregate to/from the database.
 *
 * CipherSweet encrypts PII fields:
 *  - High: nik, first_name, last_name, family_card_number, family_card_head_of_family_name
 *  - Medium: birth_place, birth_date, family_card_address
 *  - Low: type, gender — not encrypted
 */
class BeneficiaryModel extends Model implements CipherSweetEncrypted
{
    use UsesCipherSweet;

    protected $table = 'beneficiaries';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = true;

    protected $fillable = [
        'id',
        'nik',
        'nik_blind_index',
        'type',
        'first_name',
        'last_name',
        'nick_name',
        'birth_place',
        'birth_date',
        'gender',
        'family_card_number',
        'family_card_head_of_family_name',
        'family_card_address',
        'is_active',
        'specific_attributes',
        'guardians',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'specific_attributes' => BeneficiaryAttributesCast::class,
        'guardians' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Define which columns are encrypted by CipherSweet.
     *
     * Blind index on NIK allows efficient lookup without decrypting all rows.
     */
    public static function configureCipherSweet(EncryptedRow $encryptedRow): void
    {
        $encryptedRow->addTextField('nik');
        $encryptedRow->addTextField('first_name');
        $encryptedRow->addTextField('last_name');
        $encryptedRow->addTextField('birth_place');
        $encryptedRow->addTextField('birth_date');
        $encryptedRow->addTextField('family_card_number');
        $encryptedRow->addTextField('family_card_head_of_family_name');
        $encryptedRow->addTextField('family_card_address');

        $encryptedRow->addBlindIndex('nik', new BlindIndex('nik_blind_index', 32));
    }

    /**
     * Convert Eloquent model to domain Beneficiary entity.
     */
    public function toDomainEntity(): Beneficiary
    {
        // Reconstitute FamilyCard
        $familyCard = FamilyCard::reconstitute(
            createdAt: $this->created_at->toDateTimeImmutable(),
            updatedAt: $this->updated_at?->toDateTimeImmutable(),
            number: $this->family_card_number,
            headOfFamilyName: $this->family_card_head_of_family_name,
            address: isset($this->family_card_address)
                ? Address::fromArray($this->family_card_address)
                : null,
            id: DomainId::fromString($this->id),
        );

        // Reconstitute Guardians
        $guardians = array_map(
            fn (array $gData): Guardian => Guardian::reconstitute(
                id: DomainId::fromString($gData['id']),
                createdAt: new DateTimeImmutable($gData['created_at']),
                updatedAt: isset($gData['updated_at'])
                    ? new DateTimeImmutable($gData['updated_at'])
                    : null,
                beneficiaryId: DomainId::fromString($gData['beneficiary_id']),
                person: Person::fromArray($gData['person']),
                relationship: GuardianRelationship::from($gData['relationship']),
            ),
            $this->guardians ?? [],
        );

        // Reconstitute Beneficiary
        return Beneficiary::reconstitute(
            createdAt: $this->created_at->toDateTimeImmutable(),
            updatedAt: $this->updated_at?->toDateTimeImmutable(),
            name: new Name(
                firstName: $this->first_name,
                lastName: $this->last_name,
            ),
            nickName: $this->nick_name,
            birthPlace: $this->birth_place,
            birthDate: $this->birth_date,
            gender: Gender::from($this->gender),
            familyCard: $familyCard,
            isActive: $this->is_active,
            specificAttributes: $this->buildSpecificAttributes(),
            guardians: $guardians,
            id: DomainId::fromString($this->id),
            nik: new NationalIdentityNumber($this->nik),
            type: BeneficiaryType::from($this->type),
        );
    }

    /**
     * Create Eloquent model from a domain Beneficiary entity.
     */
    public static function fromDomainEntity(Beneficiary $beneficiary): self
    {
        // Serialize guardians to JSON-compatible array
        $guardiansData = array_map(
            fn (Guardian $guardian): array => [
                'id' => $guardian->id()->value,
                'beneficiary_id' => $guardian->beneficiaryId()->value,
                'person' => $guardian->person()->toArray(),
                'relationship' => $guardian->relationship()->value,
                'created_at' => $guardian->createdAt()->format('Y-m-d H:i:s'),
                'updated_at' => $guardian->updatedAt()?->format('Y-m-d H:i:s'),
            ],
            $beneficiary->guardians(),
        );

        $model = new self;
        $model->id = $beneficiary->id()->value;
        $model->nik = $beneficiary->nik()->value;
        $model->nik_blind_index = ''; // Will be computed by CipherSweet
        $model->type = $beneficiary->type()->value;
        $model->first_name = $beneficiary->name()->firstName;
        $model->last_name = $beneficiary->name()->lastName;
        $model->nick_name = $beneficiary->nickName();
        $model->birth_place = $beneficiary->birthPlace();
        $model->birth_date = $beneficiary->birthDate();
        $model->gender = $beneficiary->gender()->value;

        // Family card data
        $model->family_card_number = $beneficiary->familyCard()->number();
        $model->family_card_head_of_family_name = $beneficiary->familyCard()->headOfFamilyName();
        $model->family_card_address = $beneficiary->familyCard()->address()?->toArray();

        $model->is_active = $beneficiary->isActive();
        $model->specific_attributes = $beneficiary->specificAttributes()?->toArray();
        $model->guardians = $guardiansData === [] ? null : $guardiansData;

        return $model;
    }

    private function buildSpecificAttributes(): ?SpecificAttributes
    {
        if ($this->specific_attributes === null) {
            return null;
        }

        // Determine which SpecificAttributes implementation to use
        // For now, only ChildAttributes exists. Extend as new types are added.
        return ChildAttributes::fromArray($this->specific_attributes);
    }
}
