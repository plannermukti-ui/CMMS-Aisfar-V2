# FilamentPHP Conventions

## Panel

- Panel ID: `admin`, path: `/admin`
- Warna primary: `Color::Amber`
- Login via Filament bawaan (`.login()`)
- Resource discovery: otomatis dari `app/Filament/Resources/`

## Struktur Resource (Per Modul)

Setiap Filament Resource ditempatkan dalam sub-folder bernama sesuai modul:

```
app/Filament/Resources/
└── {ModuleName}/
    ├── {ModuleName}Resource.php
    ├── Pages/
    │   ├── List{ModuleName}.php
    │   ├── Create{ModuleName}.php
    │   ├── Edit{ModuleName}.php
    │   └── View{ModuleName}.php
    ├── Schemas/
    │   ├── {ModuleName}Form.php     → form fields
    │   └── {ModuleName}Infolist.php → view fields
    └── Tables/
        └── {ModuleName}Table.php   → table columns & actions
```

## Generate Resource (Command)

```bash
php artisan make:filament-resource {ModuleName} --generate
# Jawab: title attribute = name (atau kolom utama)
# Jawab: view page = yes
```

## Form Fields

- Gunakan `Select` (bukan `TextInput`) untuk kolom `status` dengan options eksplisit
- `FileUpload` untuk foto/gambar, bukan `TextInput`
- Jangan tampilkan `created_by`, `updated_by` di form (diisi otomatis)

## Table Columns

- Kolom `id` selalu `toggleable(isToggledHiddenByDefault: true)`
- Kolom `status` gunakan `->badge()->color(fn...)` 
- Kolom audit (`created_by`, `updated_by`, `created_at`, `updated_at`) selalu hidden by default
- Gunakan `TrashedFilter::make()` untuk resource dengan SoftDeletes
- Sertakan `ForceDeleteBulkAction`, `RestoreBulkAction` di bulk actions

## Status Badge Colors

```php
->color(fn (string $state): string => match ($state) {
    'active' => 'success',
    'inactive' => 'danger',
    'maintenance' => 'warning',
    'retired' => 'gray',
    default => 'gray',
})
```

## User Model Requirements (untuk Filament auth)

Model `User` wajib implement:
- `FilamentUser` → method `canAccessPanel(Panel $panel): bool`
- `HasName` → method `getFilamentName(): string` (pakai `full_name ?? username ?? 'Admin'`)
