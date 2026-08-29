# Architecture — CMMS PT MAM (Site Harindo Wahana)

## Stack

- **Laravel 13** + PHP 8.4
- **FilamentPHP v5** — UI Panel admin di `/admin` (BUKAN Metronic, Blade manual, atau Inertia)
- **MySQL 8.0+** — database utama
- **Laravel Sanctum** — autentikasi API
- **Livewire v4** — bawaan Filament, tidak dipakai secara mandiri

## Struktur `app/` yang Dipakai

```
app/
├── Models/          → Eloquent models, semua extend BaseModel
├── Observers/       → ActivityObserver untuk audit log otomatis
├── Http/
│   ├── Controllers/Api/   → API resource controllers (Sanctum)
│   └── Requests/          → FormRequest validasi + authorize()
├── Filament/
│   ├── Resources/   → Filament Resources (CRUD UI)
│   ├── Pages/       → Custom Filament pages
│   └── Widgets/     → Dashboard widgets
├── Policies/        → Laravel Gate policies per model
└── Providers/
    └── Filament/    → AdminPanelProvider (panel config)
```

## Tidak Ada

- Tidak ada repository layer — controller/Filament query Eloquent langsung
- Tidak ada service layer — logika bisnis di controller + FormRequest
- Tidak ada Inertia.js, Vue.js, React
- Tidak ada Blade manual untuk CRUD — **semua CRUD via Filament Resource**

## Modul Utama Saat Ini

- `equipment` — Filament Resource + API endpoint

## Setiap Modul Baru Harus Memiliki

1. Migration (UUID, softDeletes, audit columns)
2. Model (extends BaseModel)
3. Filament Resource (di `app/Filament/Resources/{Module}/`)
4. Policy (di `app/Policies/`)
5. API Resource Controller (di `app/Http/Controllers/Api/`)
6. FormRequest Store + Update (di `app/Http/Requests/`)
7. Permission Seeder entries
8. Feature Test
