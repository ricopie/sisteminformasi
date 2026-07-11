# Coding Conventions

## PHP

- PHP 8.2+ : constructor promotion, readonly properties, named arguments, backed enums
- `declare(strict_types=1)` di boundary layer: DTO, UseCase, Controller, Repository
- Entity & ValueObject: strict_types opsional (tipe sudah dijamin constructor)
- `final readonly class` untuk ValueObjects
- Named arguments untuk method dengan 3+ parameter

```php
// 👍 Baik
new Address(
    street: 'Jl. Merdeka',
    rt: '01',
    rw: '02',
    village: 'Kelurahan',
    city: 'Jakarta',
);

// 👎 Hindari
new Address('Jl. Merdeka', '01', '02', 'Kelurahan', 'Jakarta');
```

---

## Naming Conventions

| Context | Style | Contoh |
|---------|-------|--------|
| Class | `PascalCase` | `RegisterBeneficiaryUseCase` |
| Interface | `PascalCase` | `BeneficiaryRepositoryInterface` |
| Trait | `PascalCase` (adjective) | `Auditable`, `HasUlids` |
| Method | `camelCase` | `findByNik()`, `pullDomainEvents()` |
| Function | `camelCase` | `config()`, `auth()` |
| Variable | `camelCase` | `$beneficiaryData`, `$familyCard` |
| Constant | `UPPER_SNAKE` | `MAX_GUARDIANS`, `TYPE_CHILD` |
| Enum case | `UPPER_SNAKE` | `BeneficiaryType::CHILD` |
| DB Table | `snake_case` (plural) | `family_card`, `audit_logs` |
| DB Column | `snake_case` | `full_name`, `family_card_id` |
| Migration | `YYYY_MM_DD_HHMMSS_create_{table}_table.php` | `2026_07_12_000001_create_audit_logs_table.php` |

### File Naming

| Class | File |
|-------|------|
| `RegisterBeneficiaryUseCase` | `RegisterBeneficiaryUseCase.php` |
| `BeneficiaryRepositoryInterface` | `BeneficiaryRepositoryInterface.php` |
| `Auditable` (trait) | `Auditable.php` |

---

## Directory Structure

### Module Baru

```
src/
├── Domain/{Module}/
│   ├── Entities/
│   ├── ValueObjects/
│   ├── Repositories/        (interface)
│   ├── Events/
│   └── Exceptions/
├── Application/{Module}/
│   ├── UseCases/
│   └── DTOs/
├── Infrastructure/{Module}/
│   ├── Models/
│   ├── Migrations/
│   ├── Repositories/        (implementasi)
│   ├── Casts/
│   └── Providers/
└── Presentation/{Module}/
    └── Http/
        ├── Controllers/
        ├── Requests/
        └── Resources/
```

Gunakan `php artisan make:module {name}` untuk generate struktur ini.

---

## Architecture Rules

### Entity
- Berisi behaviour, bukan getter/setter
- `protected function __construct()` — gunakan `fromPersistence()` untuk reconstitute
- Factory method: `Beneficiary::register(...)` untuk create baru
- Mutator: `rename()`, `updateBirthInfo()`, `changeGender()` — bukan setter

### ValueObject
- `final readonly class`
- Immutable — tidak ada setter
- Validasi di constructor
- `equals()` method untuk perbandingan

### UseCase
- Satu class per action bisnis
- `final readonly class`
- Inject repository interface + event dispatcher
- Handle → validasi → entity logic → persist → dispatch events → return

### Controller
- Tidak ada business logic
- Parse request → DTO → panggil UseCase → return response
- HTTP status code yang tepat (201 create, 200 success, 404 not found)

### Repository
- Interface di `Domain/{Module}/Repositories/`
- Implementasi di `Infrastructure/{Module}/Repositories/`
- Return Entity, bukan Model
- Method: `findById()`, `findByNik()`, `save()`, `delete()`, `existsByNik()`

### DTO
- Gunakan Spatie LaravelData
- Validasi tipe di constructor
- `from()` untuk create dari array request
- `toArray()` untuk response

---

## Database

### Migration
- Berlokasi di `src/Infrastructure/{Module}/Migrations/`
- Bukan di `database/migrations/` (kecuali default Laravel)
- ULID primary key: `$table->ulid('id')->primary()`
- Timestamps wajib
- Soft deletes di model utama

### Naming
- Table: `snake_case`, singular (`beneficiary`, `family_card`, `audit_logs`)
- FK column: `{table_singular}_id` (`family_card_id`)
- Index: prefix `idx_` atau pakai default Laravel

---

## Testing

- PHPUnit 11 dengan `#[Test]` attribute
- Nama method: `it_can_{action}_{condition}`

```php
#[Test]
public function it_can_register_a_new_beneficiary(): void { ... }

#[Test]
public function it_throws_exception_when_nik_already_exists(): void { ... }
```

### Test Structure

| Test | Lokasi |
|------|--------|
| Unit Test Entity | `tests/Unit/Entities/` |
| Unit Test ValueObject | `tests/Unit/ValueObjects/` |
| Unit Test UseCase | `tests/Unit/UseCases/` |
| Unit Test DTO | `tests/Unit/DTOs/` |
| Unit Test Exception | `tests/Unit/Exceptions/` |
| Feature Test | `tests/Feature/` |

### UseCase Test Pattern

```php
#[Test]
public function it_can_register_a_new_beneficiary(): void
{
    // Mock repository
    $beneficiaries = Mockery::mock(BeneficiaryRepositoryInterface::class);
    $beneficiaries->shouldReceive('existsByNik')->once()->andReturnFalse();

    // Execute
    $useCase = new RegisterBeneficiaryUseCase($beneficiaries, ...);
    $result = $useCase->handle($data);

    // Assert
    $this->assertInstanceOf(Beneficiary::class, $result);
    $this->assertEquals('Budi', $result->fullName());
}
```

---

## Domain Events

- Event class extends base Event (implements `ShouldDispatch`)
- Dipanggil via `$this->events->dispatch()` di UseCase
- Entity mengumpulkan events via `pullDomainEvents()`
- Listener: 🔜 (belum implementasi)

```php
// Entity
public function pullDomainEvents(): array
{
    $events = $this->events;
    $this->events = [];

    return $events;
}

// UseCase
foreach ($beneficiary->pullDomainEvents() as $event) {
    $this->events->dispatch($event);
}
```

---

## Audit Log

- Trait `Auditable` di `Infrastructure/Audit/Concerns/`
- Pasang di Eloquent Model: `use Auditable;`
- Otomatis log `created` / `updated` / `deleted`
- Detail: docs/features/audit-log.md

---

## Code Quality

Sebelum commit:

```bash
composer analyse    # PHPStan level 5
composer test       # PHPUnit
composer format     # Pint auto-fix
composer test       # Verifikasi
composer rector     # Rector dry-run
```

### Tools

| Tool | Config | Fungsi |
|------|--------|--------|
| PHPStan | `phpstan.neon` (level 5) | Static analysis |
| Pint | Laravel preset | Code style |
| Rector | `rector.php` | Refactoring (dry-run) |

---

## Git

### Branch

```
feat/{nama}       → Fitur baru
fix/{nama}        → Bug fix
refactor/{nama}   → Refactoring
docs/{nama}       → Dokumentasi
```

### Commit Message

```
type(scope): description

type: feat | fix | refactor | docs | style | chore
scope: module atau fitur (opsional)
```

Contoh:

```
feat(audit): add auditable trait for model changes tracking
fix(beneficiary): validate NIK length before persist
refactor: extract address creation to shared value object
docs: add architecture overview documentation
```
