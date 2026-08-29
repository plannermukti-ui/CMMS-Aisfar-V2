# Modul Equipment — Dokumentasi

**Versi:** 1.0  
**Status:** Active  
**Stack:** Laravel 13, FilamentPHP v5, Sanctum API

---

## Deskripsi

Modul Equipment mengelola data inventaris peralatan/mesin di PT MAM Site Harindo Wahana. Setiap peralatan memiliki kode unik, nama, status operasional, dan dapat dilampirkan foto.

---

## Database

**Tabel:** `equipments`

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | UUID PK | Auto-generated UUID |
| `equipment_code` | string(50) unique | Kode peralatan (e.g., EQP-001) |
| `equipment_name` | string(255) | Nama peralatan |
| `status` | string | `active`, `inactive`, `maintenance`, `retired` |
| `description` | text nullable | Deskripsi peralatan |
| `photo` | string nullable | Path foto di storage |
| `deleted_at` | timestamp | Soft delete |
| `created_by` | foreignUuid | User yang membuat |
| `updated_by` | foreignUuid | User yang terakhir update |
| `created_at` | timestamp | Auto |
| `updated_at` | timestamp | Auto |

---

## Permissions

| Permission | Keterangan |
|---|---|
| `view equipment` | Melihat daftar & detail |
| `create equipment` | Membuat data baru |
| `edit equipment` | Mengedit & restore |
| `delete equipment` | Menghapus & force delete |
| `export equipment` | Mengekspor data |

---

## File-file Terkait

| Tipe | Path |
|---|---|
| Migration | `database/migrations/2026_08_27_234446_create_equipment_table.php` |
| Model | `app/Models/Equipment.php` |
| Policy | `app/Policies/EquipmentPolicy.php` |
| Filament Resource | `app/Filament/Resources/Equipment/EquipmentResource.php` |
| API Controller | `app/Http/Controllers/Api/EquipmentController.php` |
| Form Request (Store) | `app/Http/Requests/StoreEquipmentRequest.php` |
| Form Request (Update) | `app/Http/Requests/UpdateEquipmentRequest.php` |
| Feature Test | `tests/Feature/EquipmentCrudTest.php` |

---

## API Endpoints

Base URL: `/api/v1/equipment`  
Auth: Bearer Token (Sanctum)

| Method | Endpoint | Permission | Keterangan |
|---|---|---|---|
| GET | `/api/v1/equipment` | `view equipment` | List semua equipment (paginated) |
| POST | `/api/v1/equipment` | `create equipment` | Buat equipment baru |
| GET | `/api/v1/equipment/{id}` | `view equipment` | Detail satu equipment |
| PUT | `/api/v1/equipment/{id}` | `edit equipment` | Update equipment |
| DELETE | `/api/v1/equipment/{id}` | `delete equipment` | Soft delete equipment |

---

## Validasi

| Field | Rules |
|---|---|
| `equipment_code` | required, string, max:50, unique |
| `equipment_name` | required, string, max:100 |
| `status` | required, in:active,inactive,maintenance,retired |
| `description` | nullable, string, max:1000 |
| `photo` | nullable, image, mimes:jpeg/png/webp, max:2MB |
