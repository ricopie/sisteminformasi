# Audit Log

## Overview

Audit log mencatat setiap perubahan data secara otomatis. Setiap **create**, **update**, atau **delete** pada model yang menggunakan trait `Auditable` akan tercatat di tabel `audit_logs`.

Fitur ini bersifat **cross-cutting infrastructure** — tidak ada perubahan di Domain atau Application layer. Cukup pasang trait di Eloquent Model.

---

## Struktur

```
src/Infrastructure/Audit/
├── Concerns/Auditable.php              # Trait — hook Eloquent events
├── Models/AuditLogModel.php            # Query log
├── Migrations/xxxx_create_audit_logs_table.php
└── Providers/AuditServiceProvider.php  # Load migration
```

Tabel `audit_logs`:

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key |
| `user_id` | bigint, nullable | FK ke users |
| `event` | string | `created`, `updated`, atau `deleted` |
| `auditable_type` | string | Class model (contoh: `Infrastructure\Beneficiaries\Models\BeneficiaryModel`) |
| `auditable_id` | string | ULID record yang berubah |
| `old_values` | json, nullable | Data sebelum perubahan |
| `new_values` | json, nullable | Data setelah perubahan |
| `created_at` | timestamp | Kapan perubahan terjadi |
| `updated_at` | timestamp | - |

---

## Cara Pakai

### 1. Pasang Trait di Model

```php
use Infrastructure\Audit\Concerns\Auditable;

class BeneficiaryModel extends Model
{
    use Auditable;
    // ...
}
```

### 2. Otomatis — Tidak Perlu Lagi

Setiap operasi Eloquent akan terekam:

```php
$model = BeneficiaryModel::create([...]);
// ✅ INSERT audit_logs (event: created, new_values: {...})

$model->update(['full_name' => 'Budi Baru']);
// ✅ INSERT audit_logs (event: updated, old_values: {...}, new_values: {...})

$model->delete();
// ✅ INSERT audit_logs (event: deleted, old_values: {...})
```

### 3. Query Log

```php
use Infrastructure\Audit\Models\AuditLogModel;

// Semua log untuk record tertentu
$logs = AuditLogModel::where('auditable_type', BeneficiaryModel::class)
    ->where('auditable_id', $id)
    ->get();

// Log by user
$logs = AuditLogModel::where('user_id', auth()->id())->get();

// Log by event
$logs = AuditLogModel::where('event', 'updated')->get();
```

---

## Yang Dicatat

| Event | old_values | new_values |
|-------|-----------|------------|
| `created` | `null` | Semua atribut setelah create |
| `updated` | Atribut sebelum berubah | Atribut setelah berubah |
| `deleted` | Semua atribut sebelum dihapus | `null` |

**Tidak dicatat:** Field sensitif seperti password tidak termasuk karena sudah di-filter oleh `$model->getAttributes()` — pastikan field password tidak di-expose.

---

## Integrasi dengan Auth

Log otomatis mengambil `auth()->id()`. Jika tidak ada user login (misalnya dari command/queue), `user_id` akan `null`.

```php
AuditLogModel::create([
    'user_id' => auth()->id(),  // null jika tidak login
    // ...
]);
```

---

## Model Relasi

```php
class AuditLogModel extends Model
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }
}
```

Contoh query dengan relasi:

```php
$logs = AuditLogModel::with('user')->latest()->get();

foreach ($logs as $log) {
    echo $log->user?->name;       // siapa
    echo $log->event;             // ngapain
    echo $log->auditable_type;    // data apa
    echo $log->created_at;        // kapan
}
```

---

## Scope & Filtering

Tambahkan local scope untuk filtering umum:

```php
// Model AuditLogModel
public function scopeForModel(Builder $query, Model $model): Builder
{
    return $query->where('auditable_type', $model::class)
        ->where('auditable_id', $model->getKey());
}

public function scopeByEvent(Builder $query, string $event): Builder
{
    return $query->where('event', $event);
}
```

---

## Model yang sudah menggunakan Auditable

| Model | File |
|-------|------|
| `BeneficiaryModel` | `src/Infrastructure/Beneficiaries/Models/BeneficiaryModel.php` |
| `FamilyCardModel` | `src/Infrastructure/Beneficiaries/Models/FamilyCardModel.php` |

Untuk model baru, cukup tambahkan `use Auditable;` — tidak perlu konfigurasi lain.
