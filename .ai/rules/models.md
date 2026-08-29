# Model Conventions

## BaseModel (WAJIB dipakai semua model entitas bisnis)

Semua model extend `App\Models\BaseModel`, bukan `Illuminate\Database\Eloquent\Model` langsung.

```php
class WorkOrder extends BaseModel
{
    protected $table = 'work_orders';
    protected $fillable = [...];
}
```

`BaseModel` sudah menyertakan `HasUuids` dan `SoftDeletes`. Jangan tambahkan ulang.

## User Model (Exception)

`User` extend `Authenticatable` (bukan `BaseModel`), namun tetap memakai:
- `HasUuids`
- `SoftDeletes`
- implements `FilamentUser`, `HasName`
- `User` model sudah memiliki method `hasRole(string $role): bool` dan `hasPermission(string $permission): bool`. Selalu gunakan fungsi bawaan ini.

## $fillable Ketat (WAJIB)

```php
protected $fillable = ['field1', 'field2', ...];
```

DILARANG keras menggunakan `protected $guarded = []`. Gunakan whitelist `$fillable` yang eksplisit.

## Kolom `created_by` dan `updated_by` di $fillable

Selalu masukkan `created_by` dan `updated_by` ke `$fillable` karena diisi manual di controller.

## Relasi Audit Wajib

Setiap model yang memiliki `created_by` / `updated_by` harus mendefinisikan:

```php
public function createdBy(): BelongsTo
{
    return $this->belongsTo(User::class, 'created_by');
}

public function updatedBy(): BelongsTo
{
    return $this->belongsTo(User::class, 'updated_by');
}
```

## Observer

Setiap model bisnis baru harus diobserve oleh `ActivityObserver` di `AppServiceProvider`:

```php
\App\Models\WorkOrder::observe(\App\Observers\ActivityObserver::class);
```

## $hidden Default dari BaseModel

`deleted_at` sudah di-hidden di `BaseModel`. Jangan tambahkan ulang.
