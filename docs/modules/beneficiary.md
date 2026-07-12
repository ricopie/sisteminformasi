# Beneficiary Module

## Overview

Module untuk manajemen data penerima manfaat. Mendukung empat tipe penerima:

| Tipe | Kode | Specific Attributes |
|------|------|-------------------|
| Umum | `GENERAL` | Tidak ada |
| Lansia | `ELDERLY` | Tidak ada |
| Disabilitas | `DISABLED` | Tidak ada |
| Anak | `CHILD` | Pendidikan, hobi |

---

## Architecture

```
Domain/Beneficiaries/
├── Entities/
│   ├── Beneficiary.php          # Aggregate root
│   ├── FamilyCard.php           # Aggregate root (independent)
│   └── Guardian.php             # Child entity dalam Beneficiary
├── ValueObjects/
│   ├── Child/
│   │   ├── ChildAttributes.php  # Specific attributes untuk CHILD
│   │   └── Education.php        # Riwayat pendidikan
│   ├── Enum/
│   │   ├── BeneficiaryType.php  # GENERAL, ELDERLY, DISABLED, CHILD
│   │   ├── EducationLevel.php   # SD, SMP, SMA, D3, S1, etc
│   │   ├── EducationStatus.php  # ENROLLED, GRADUATED, DROPPED_OUT
│   │   └── GuardianRelationship.php # FATHER, MOTHER, etc
│   └── SpecificAttributes.php   # Polymorphic attributes container
├── Repositories/
│   ├── BeneficiaryRepositoryInterface.php
│   └── FamilyCardRepositoryInterface.php
├── Events/
│   ├── BeneficiaryRegistered.php
│   ├── BeneficiaryUpdated.php
│   └── BeneficiaryDeleted.php
└── Exceptions/
    └── BeneficiaryAlreadyExistsException.php

Application/Beneficiaries/
├── DTOs/
│   ├── RegisterBeneficiaryData.php
│   ├── UpdateBeneficiaryData.php
│   ├── BeneficiaryData.php
│   ├── FamilyCardData.php
│   ├── GuardianData.php
│   └── Child/
│       ├── ChildAttributesData.php
│       └── EducationData.php
├── Queries/
│   └── BeneficiaryQueryInterface.php
└── UseCases/
    ├── RegisterBeneficiaryUseCase.php
    ├── UpdateBeneficiaryUseCase.php
    ├── DeleteBeneficiaryUseCase.php
    ├── GetBeneficiaryUseCase.php
    └── ListBeneficiariesUseCase.php

Infrastructure/Beneficiaries/
├── Models/
│   ├── BeneficiaryModel.php
│   ├── FamilyCardModel.php
│   └── GuardianModel.php
├── Migrations/
│   ├── 2026_06_22_041424_create_family_card_table.php
│   ├── 2026_06_22_041443_create_beneficiary_table.php
│   └── 2026_07_04_000001_create_guardians_table.php
├── Repositories/
│   ├── EloquentBeneficiaryRepository.php
│   ├── EloquentFamilyCardRepository.php
│   └── EloquentGuardianRepository.php
├── Queries/
│   └── EloquentBeneficiaryQuery.php
├── Casts/
│   └── BeneficiaryAttributesCast.php
└── Providers/
    └── BeneficiaryServiceProvider.php

Presentation/Beneficiaries/Http/
└── Controllers/
    ├── RegisterBeneficiaryController.php
    ├── UpdateBeneficiaryController.php
    ├── DeleteBeneficiaryController.php
    ├── GetBeneficiaryController.php
    └── ListBeneficiariesController.php
```

---

## Domain Layer

### Entities

#### Beneficiary

Aggregate root untuk data penerima manfaat.

```php
final class Beneficiary extends BaseEntity
{
    // Factory
    public static function register(
        NationalIdentityNumber $nik,
        BeneficiaryType $type,
        PersonName $fullName,
        PersonName $nickName,
        string $birthPlace,
        CarbonImmutable $birthDate,
        Gender $gender,
        DomainId $familyCardId,
        ?SpecificAttributes $specificAttributes,
    ): self;

    // Mutators (behaviour, bukan setter)
    public function rename(PersonName $fullName, PersonName $nickName): void;
    public function updateBirthInfo(string $birthPlace, CarbonImmutable $birthDate): void;
    public function changeGender(Gender $gender): void;
    public function updateSpecificAttributes(?SpecificAttributes $attributes): void;
    public function assignToFamilyCard(DomainId $familyCardId): void;

    // Guardian management
    public function addGuardian(Person $person, GuardianRelationship $relationship): void;
    public function removeAllGuardians(): void;

    // Getters
    public function guardians(): array;
    // ...
}
```

#### FamilyCard

Aggregate root untuk Kartu Keluarga. Independent — bisa di-share antar beneficiary.

```php
final class FamilyCard extends BaseEntity
{
    public static function register(
        string $number,
        PersonName $headOfFamilyName,
        ?Address $address,
    ): self;

    public function updateAddress(Address $address): void;
}
```

#### Guardian

Child entity dalam Beneficiary. Tidak bisa berdiri sendiri.

```php
final class Guardian extends BaseEntity
{
    public static function register(
        Person $person,
        GuardianRelationship $relationship,
    ): self;
}
```

### ValueObjects

#### NationalIdentityNumber

Validasi NIK 16 digit.

```php
final readonly class NationalIdentityNumber
{
    public function __construct(public string $value)
    {
        if (! preg_match('/^\d{16}$/', $value)) {
            throw new InvalidArgumentException('NIK must be exactly 16 digits.');
        }
    }
}
```

#### BeneficiaryType

```php
enum BeneficiaryType: string
{
    case GENERAL = 'general';
    case ELDERLY = 'elderly';
    case DISABLED = 'disabled';
    case CHILD = 'child';
}
```

#### ChildAttributes

Specific attributes khusus tipe CHILD.

```php
final readonly class ChildAttributes extends SpecificAttributes
{
    public function __construct(
        public Education $education,
        public array $educationHistory,  // Education[]
        public array $hobbies,           // string[]
    ) {}
}
```

#### Education

```php
final readonly class Education
{
    public function __construct(
        public EducationLevel $level,
        public EducationStatus $status,
        public string $schoolName,
        public int $grade,
        public ?string $major,
        public ?string $nisn,
    ) {}
}
```

### Domain Events

| Event | Trigger | Data |
|-------|---------|------|
| `BeneficiaryRegistered` | Register beneficiary | ID, NIK, tipe, timestamp |
| `BeneficiaryUpdated` | Update beneficiary | ID, field yang berubah |
| `BeneficiaryDeleted` | Delete beneficiary | ID, NIK |

Event di-dispatch di UseCase, bukan di Entity:

```php
foreach ($beneficiary->pullDomainEvents() as $event) {
    $this->events->dispatch($event);
}
```

### Exceptions

| Exception | Method | When |
|-----------|--------|------|
| `BeneficiaryAlreadyExistsException` | `forNik()` | NIK sudah terdaftar |
| `EntityNotFoundException` | `forId()` | ID tidak ditemukan |
| `InvalidIdentifierException` | `for()` | ID format salah |
| `BeneficiaryAttributeException` | `missingAttributes()` / `attributesNotAllowed()` | Specific attributes mismatch |

---

## Application Layer

### Use Cases

#### RegisterBeneficiaryUseCase

```php
final readonly class RegisterBeneficiaryUseCase
{
    public function handle(RegisterBeneficiaryData $data): Beneficiary;
}
```

**Flow:**
1. Cek NIK unik
2. Cari FamilyCard by nomor KK, buat baru jika belum ada
3. Buat SpecificAttributes sesuai tipe (CHILD → ChildAttributes, lainnya → null)
4. Register Beneficiary entity
5. Tambah Guardians (jika ada)
6. Persist via repository (DB transaction)
7. Dispatch domain events

#### UpdateBeneficiaryUseCase

```php
final readonly class UpdateBeneficiaryUseCase
{
    public function handle(string $id, UpdateBeneficiaryData $data): Beneficiary;
}
```

**Flow:**
1. Cari Beneficiary by ID
2. Update field yang disediakan (partial update)
3. Update FamilyCard jika nomor KK baru
4. Update SpecificAttributes jika tipe berubah
5. Replace Guardians (full replacement)
6. Persist
7. Dispatch domain events

#### DeleteBeneficiaryUseCase

```php
final readonly class DeleteBeneficiaryUseCase
{
    public function handle(string $id): void;
}
```

**Flow:**
1. Cari Beneficiary by ID
2. Soft delete
3. Dispatch domain events

#### GetBeneficiaryUseCase

```php
final readonly class GetBeneficiaryUseCase
{
    public function handle(string $id): Beneficiary;
}
```

#### ListBeneficiariesUseCase

```php
final readonly class ListBeneficiariesUseCase
{
    public function handle(
        array $filters,    // type, search
        int $perPage = 15,
        int $page = 1,
    ): LengthAwarePaginator;
}
```

**Filters:**
- `type` — filter by BeneficiaryType
- `search` — cari berdasarkan nama, NIK, nomor KK

### DTOs

Semua DTO menggunakan Spatie LaravelData:

```php
class RegisterBeneficiaryData extends Data
{
    public function __construct(
        public NationalIdentityNumber $nik,
        public BeneficiaryType $type,
        public string $fullName,
        public string $nickName,
        public string $birthPlace,
        public CarbonImmutable $birthDate,
        public Gender $gender,
        public FamilyCardData $familyCard,
        /** @var array<string, mixed>|null */
        public ?array $specificAttributes,
        /** @var GuardianData[]|null */
        public ?array $guardians,
    ) {}
}
```

---

## Infrastructure Layer

### Models

| Model | Table | Extends |
|-------|-------|---------|
| `BeneficiaryModel` | `beneficiary` | `Model` |
| `FamilyCardModel` | `family_card` | `Model` |
| `GuardianModel` | `guardians` | `Model` |

Semua model menggunakan:
- **HasUlids** — ULID primary key
- **SoftDeletes** — soft delete
- **UsesCipherSweet** — encrypted PII fields

### Repositories

| Interface | Implementation |
|-----------|---------------|
| `BeneficiaryRepositoryInterface` | `EloquentBeneficiaryRepository` |
| `FamilyCardRepositoryInterface` | `EloquentFamilyCardRepository` |

Repository method:
- `findById(string $id): ?Entity`
- `save(Entity $entity): void`
- `delete(Entity $entity): void`
- Method spesifik: `existsByNik()`, `findByNumber()`

### Queries

```php
interface BeneficiaryQueryInterface
{
    public function paginate(array $filters, int $perPage, int $page): LengthAwarePaginator;
}
```

---

## Presentation Layer

### API Endpoints

Semua endpoint prefix `/beneficiaries`.

| Method | URI | Controller | UseCase |
|--------|-----|------------|---------|
| `POST` | `/beneficiaries` | `RegisterBeneficiaryController` | `RegisterBeneficiaryUseCase` |
| `GET` | `/beneficiaries` | `ListBeneficiariesController` | `ListBeneficiariesUseCase` |
| `GET` | `/beneficiaries/{id}` | `GetBeneficiaryController` | `GetBeneficiaryUseCase` |
| `PUT` | `/beneficiaries/{id}` | `UpdateBeneficiaryController` | `UpdateBeneficiaryUseCase` |
| `DELETE` | `/beneficiaries/{id}` | `DeleteBeneficiaryController` | `DeleteBeneficiaryUseCase` |

### Request Example

**POST /api/beneficiaries**

```json
{
    "nik": "3201010101010001",
    "type": "child",
    "fullName": "Budi Hartono",
    "nickName": "Budi",
    "birthPlace": "Jakarta",
    "birthDate": "2010-06-15",
    "gender": "male",
    "familyCard": {
        "number": "320101010101",
        "head_of_family_name": "Bambang",
        "address": {
            "street": "Jl. Merdeka No. 1",
            "rt": "01",
            "rw": "02",
            "village": "Kelurahan",
            "district": "Kecamatan",
            "city": "Kota",
            "province": "Provinsi",
            "postal_code": "12345"
        }
    },
    "specificAttributes": {
        "education": {
            "level": "senior_high",
            "status": "currently_enrolled",
            "schoolName": "SMA Negeri 1",
            "grade": 12,
            "major": "IPA",
            "nisn": "1234567890"
        },
        "educationHistory": [],
        "hobbies": ["membaca", "berenang"]
    },
    "guardians": [
        {
            "person": {
                "name": "Bambang Hartono",
                "occupation": "PNS",
                "education": "s1",
                "address": {
                    "street": "Jl. Merdeka No. 1",
                    "rt": "01",
                    "rw": "02",
                    "village": "Kelurahan",
                    "district": "Kecamatan",
                    "city": "Kota",
                    "province": "Provinsi",
                    "postal_code": "12345"
                },
                "contact": {
                    "phone": "081234567890",
                    "emailAddress": "bambang@example.com"
                }
            },
            "relationship": "father"
        }
    ]
}
```

### Response Example

**201 Created**

```json
{
    "data": {
        "id": "01J5Z...",
        "nik": "3201010101010001",
        "type": "child",
        "fullName": "Budi Hartono",
        "nickName": "Budi",
        "birthPlace": "Jakarta",
        "birthDate": "2010-06-15",
        "gender": "male",
        "familyCardId": "01J5Y...",
        "specificAttributes": {
            "education": { ... },
            "educationHistory": [],
            "hobbies": ["membaca", "berenang"]
        },
        "guardians": [
            {
                "id": "01J5X...",
                "person": { ... },
                "relationship": "father"
            }
        ]
    },
    "message": "Beneficiary registered"
}
```

---

## Relationships

```
FamilyCard (1) ---< Beneficiary (1) ---< Guardian

FamilyCard:
  - id (ULID, PK)
  - number (unique)
  - head_of_family_name
  - address (json)

Beneficiary:
  - id (ULID, PK)
  - nik (unique)
  - type (enum)
  - full_name, nick_name
  - birth_place, birth_date
  - gender
  - specific_attributes (json)
  - family_card_id (FK → family_card)
  - soft deletes

Guardian:
  - id (ULID, PK)
  - beneficiary_id (FK → beneficiary)
  - name, occupation
  - education, address, contact
  - relationship (enum)
```

---

## Business Rules

1. **NIK** — harus 16 digit, unique
2. **Beneficiary type** — menentukan specific attributes yang valid:
   - `CHILD` → `ChildAttributes` (wajib)
   - `GENERAL`, `ELDERLY`, `DISABLED` → `null`
3. **FamilyCard** — bisa di-share antar beneficiary (satu KK untuk satu keluarga)
4. **Guardians** — full replacement saat update (guardian lama dihapus, diganti baru)
5. **Soft delete** — beneficiary tidak dihapus permanen, hanya di-soft-delete
