# Testing Conventions

## Framework

PHPUnit (bukan Pest). Buat test dengan:
```bash
php artisan make:test --phpunit {ModuleName}CrudTest
```

## Struktur Feature Test Per Modul

Setiap modul wajib memiliki 1 feature test file dengan test cases berikut:

```php
class EquipmentCrudTest extends TestCase
{
    use RefreshDatabase;

    // Setup: buat user admin dengan semua permissions
    protected function setUp(): void { ... }

    // CRUD Tests
    public function test_admin_can_view_equipment_list(): void {}
    public function test_admin_can_create_equipment(): void {}
    public function test_admin_can_update_equipment(): void {}
    public function test_admin_can_delete_equipment(): void {}

    // Authorization Tests
    public function test_guest_cannot_access_equipment_api(): void {}
    public function test_user_without_permission_cannot_create(): void {}

    // Validation Tests
    public function test_equipment_code_is_required(): void {}
    public function test_equipment_code_must_be_unique(): void {}
}
```

## Lokasi Test

`tests/Feature/{ModuleName}CrudTest.php`

## Data Test

Gunakan factory untuk membuat data test. Jangan hard-code nilai di banyak tempat.

## API Testing Base URL

Selalu test API endpoint dengan prefix `/api/v1/`:
```php
$this->getJson('/api/v1/equipment');
$this->postJson('/api/v1/equipment', [...]);
```
