# Authorization Conventions

## Permission Naming (WAJIB)

Format: `"{action} {module}"` — pisah spasi, huruf kecil semua.

Contoh valid:
- `view equipment`
- `create equipment`
- `edit equipment`
- `delete equipment`
- `approve work-order`
- `manage-users system`

## Gate / Policy

Setiap model bisnis wajib memiliki Policy di `app/Policies/{Model}Policy.php`.

Policy method mapping:
```php
public function viewAny(User $user): bool  → 'view {module}'
public function view(User $user, Model $m): bool → 'view {module}'
public function create(User $user): bool → 'create {module}'
public function update(User $user, Model $m): bool → 'edit {module}'
public function delete(User $user, Model $m): bool → 'delete {module}'
public function forceDelete(User $user, Model $m): bool → 'delete {module}'
public function restore(User $user, Model $m): bool → 'edit {module}'
```

Setiap policy check menggunakan helper ini di `User` model:

```php
public function hasPermission(string $permission): bool
{
    return $this->getAllPermissions()->contains('name', $permission);
}
```

## Registrasi Policy

Daftarkan di `AuthServiceProvider` (atau `AppServiceProvider`):
```php
Gate::policy(Equipment::class, EquipmentPolicy::class);
```

## Filament Resource Authorization

Di Filament Resource, tambahkan:
```php
public static function canViewAny(): bool
{
    return auth()->user()->can('view equipment');
}
```

## API Controller Authorization

Gunakan `$this->authorize('view equipment')` di setiap method API controller.
FormRequest sudah handle `create` dan `update` via `authorize()`.
