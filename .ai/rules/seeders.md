# Seeder Conventions

## Permission Seeder (`PermissionSeeder.php`)

Setiap modul baru HARUS menambahkan entry permission ke `PermissionSeeder`. Jangan buat seeder terpisah per modul.

Format array:
```php
['category' => '{module}', 'action' => 'view',   'display_name' => 'View {Module}'],
['category' => '{module}', 'action' => 'create',  'display_name' => 'Create {Module}'],
['category' => '{module}', 'action' => 'edit',    'display_name' => 'Edit {Module}'],
['category' => '{module}', 'action' => 'delete',  'display_name' => 'Delete {Module}'],
```

Tambahkan action spesifik jika diperlukan (mis. `approve`, `assign`, `export`).

Nama permission yang dihasilkan: `"{action} {category}"` → `"view work-order"`.

## UserSeeder

Admin user (`admin@example.com`) harus memiliki semua permissions. Pastikan:

```php
$adminRole->permissions()->sync(Permission::all());
```

selalu di akhir UserSeeder setelah PermissionSeeder dijalankan.

## Urutan DatabaseSeeder

```php
$this->call([
    PermissionSeeder::class, // HARUS pertama
    UserSeeder::class,       // HARUS setelah PermissionSeeder
]);
```
