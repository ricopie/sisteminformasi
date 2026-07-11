# Getting Started

## Prerequisites

- PHP 8.2+
- Composer 2.x
- SQLite (development) / MySQL 8+ (production)
- Node.js 20+ & NPM
- Docker & Laravel Sail (optional)

---

## Installation

Clone the project and install dependencies:

```bash
git clone <repository-url>
cd sisteminformasi_v1

composer install
cp .env.example .env
php artisan key:generate
```

### Development Database

Secara default, aplikasi menggunakan SQLite untuk development:

```bash
touch database/database.sqlite
php artisan migrate
```

Untuk MySQL, sesuaikan `.env`:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sisteminformasi
DB_USERNAME=root
DB_PASSWORD=
```

### Frontend (optional)

```bash
npm install
npm run build
# atau untuk development:
npm run dev
```

---

## Running the Application

```bash
php artisan serve
# http://localhost:8000
```

Atau via Laravel Sail:

```bash
./vendor/bin/sail up
```

---

## Testing

### Menjalankan semua test

```bash
php artisan test
# atau
vendor/bin/phpunit
```

### Test dengan coverage

```bash
php artisan test --coverage
```

Test ditulis dengan PHPUnit 11 menggunakan `#[Test]` attribute:

```php
use PHPUnit\Framework\Attributes\Test;

class RegisterBeneficiaryUseCaseTest extends TestCase
{
    #[Test]
    public function it_can_register_a_new_beneficiary(): void
    {
        // ...
    }
}
```

---

## Code Quality

Sebelum commit, jalankan **Final Analysis Sequence**:

```bash
composer analyse    # PHPStan — cek type safety
composer test       # Baseline — pastikan hijau
composer format     # Pint — auto-fix style
composer test       # Verifikasi formatting tidak merusak
composer rector     # Rector dry-run — review manual
```

Setiap command:

| Command | Tool | Fungsi |
|---------|------|--------|
| `composer analyse` | PHPStan level 5 | Cek type errors & logical issues |
| `composer test` | PHPUnit 11 | Jalankan semua test |
| `composer format` | Laravel Pint | Auto-format code style |
| `composer rector` | Rector | Dry-run refactor suggestions |
| `composer rector-fix` | Rector | Apply refactor (jika dibutuhkan) |

---

## Useful Commands

### Module

```bash
php artisan make:module Blog
# Membuat struktur module baru di src/
```

### Database

```bash
php artisan migrate              # Jalankan migration
php artisan migrate:rollback     # Rollback batch terakhir
php artisan migrate:fresh        # Drop semua tabel + migrate ulang
php artisan db:show              # Lihat info database
php artisan db:table audit_logs  # Lihat struktur tabel
```

### Cache

```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

## Project Structure

```
sisteminformasi_v1/
├── app/                   # Laravel application (providers, commands)
├── src/                   # DDD layers
│   ├── Domain/
│   ├── Application/
│   ├── Infrastructure/
│   ├── Presentation/
│   └── Shared/
├── config/                # Configuration files
├── database/              # Database factories, seeders
├── docs/                  # Project documentation
├── routes/                # Route definitions
├── tests/                 # PHPUnit tests
└── .opencode/             # Agent configuration & prompts
```

---

## Troubleshooting

### Migration error "table already exists"

Jika migration gagal karena tabel sudah ada:

```bash
php artisan migrate:mark-as-run <migration_name>
```

### Class not found

Jika muncul error class tidak ditemukan, regenerate autoload:

```bash
composer dump-autoload
```

### SQLite PDOException

Pastikan SQLite extension aktif di `php.ini`:

```
extension=pdo_sqlite
extension=sqlite3
```
