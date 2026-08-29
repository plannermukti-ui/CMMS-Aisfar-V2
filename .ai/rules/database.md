# Database Conventions

## UUID Primary Key (WAJIB semua tabel)

```php
$table->uuid('id')->primary();
```

Semua tabel WAJIB menggunakan UUID sebagai primary key. DILARANG menggunakan `bigIncrements()` atau `id()`.

## Soft Deletes (WAJIB semua tabel entitas)

```php
$table->softDeletes();
```

Semua tabel entitas bisnis wajib memiliki `softDeletes()`. Data tidak pernah dihapus permanen kecuali via force delete.

## Audit Columns (WAJIB semua tabel entitas)

```php
$table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
$table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
$table->timestamps();
```

Kedua kolom menggunakan `foreignUuid` → `nullable()` → `constrained('users')` → `nullOnDelete()`.

## Index Wajib

```php
$table->index('created_at');
// tambahkan index pada kolom yang sering di-query/search
$table->index('status');
```

## Naming Convention

- Primary key: `id` (uuid)
- Foreign key: `{singular_model}_id` (pakai `foreignUuid`)
- Status columns: `status` dengan nilai string enum (mis. `active`, `inactive`, `maintenance`)
- Table name: plural snake_case (mis. `work_orders`, `equipments`)

## Template Lengkap Migration Baru

```php
Schema::create('table_name', function (Blueprint $table) {
    $table->uuid('id')->primary();
    // kolom bisnis di sini
    $table->string('status')->default('active');
    $table->softDeletes();
    $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
    $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
    $table->timestamps();
    $table->index('created_at');
    $table->index('status');
});
```
