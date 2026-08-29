# Routes Conventions

## Web Routes

`routes/web.php` hanya berisi redirect ke panel Filament. Tidak ada CRUD web routes manual.

```php
Route::get('/', fn() => redirect('/admin'));
```

Semua UI CRUD ditangani oleh Filament panel di `/admin`.

## API Routes

Format: `/api/v1/{resource}` — wajib versioned.

```php
// routes/api.php
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::apiResource('equipment', \App\Http\Controllers\Api\EquipmentController::class);
    Route::apiResource('work-orders', \App\Http\Controllers\Api\WorkOrderController::class);
});
```

DILARANG menambahkan API endpoint tanpa prefix `v1` dan middleware `auth:sanctum`.

## API Controller Namespace

Semua API controller berada di `App\Http\Controllers\Api\`.

## API Response Format

Selalu kembalikan JSON dengan wrapper:
```json
{ "data": {...} }          // untuk single/list
{ "message": "...", "data": {...} }  // untuk store/update
{ "message": "..." }       // untuk delete
```

Status code: 200 (index/show), 201 (store), 200 (update), 200 (destroy), 500 (error).
