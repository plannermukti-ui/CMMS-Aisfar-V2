<?php

namespace Tests\Feature;

use App\Models\Equipment;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class EquipmentCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $userNoAccess;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed permissions
        $this->seed(PermissionSeeder::class);

        // Create admin with all permissions
        $adminRole = Role::create(['name' => 'admin', 'display_name' => 'Administrator']);
        $adminRole->permissions()->sync(Permission::all());

        $this->admin = User::create([
            'username' => 'admin',
            'full_name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
        ]);
        $this->admin->roles()->attach($adminRole);

        // Create user with no permissions
        $this->userNoAccess = User::create([
            'username' => 'noaccess',
            'full_name' => 'No Access User',
            'email' => 'noaccess@test.com',
            'password' => Hash::make('password'),
        ]);
    }

    // ─── CRUD Tests ───────────────────────────────────────────

    public function test_admin_can_view_equipment_list(): void
    {
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/v1/equipments');

        $response->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    public function test_admin_can_create_equipment(): void
    {
        $payload = [
            'equipment_code' => 'EQP-001',
            'equipment_name' => 'Pompa Air',
            'status' => 'active',
            'description' => 'Pompa air industri',
        ];

        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/v1/equipments', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.equipment_code', 'EQP-001');

        $this->assertDatabaseHas('equipments', ['equipment_code' => 'EQP-001']);
    }

    public function test_admin_can_update_equipment(): void
    {
        $equipment = Equipment::create([
            'equipment_code' => 'EQP-002',
            'equipment_name' => 'Kompresor',
            'status' => 'active',
            'created_by' => $this->admin->id,
            'updated_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->putJson("/api/v1/equipments/{$equipment->id}", [
                'equipment_code' => 'EQP-002',
                'equipment_name' => 'Kompresor Updated',
                'status' => 'maintenance',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.equipment_name', 'Kompresor Updated');
    }

    public function test_admin_can_soft_delete_equipment(): void
    {
        $equipment = Equipment::create([
            'equipment_code' => 'EQP-003',
            'equipment_name' => 'Genset',
            'status' => 'active',
            'created_by' => $this->admin->id,
            'updated_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->deleteJson("/api/v1/equipments/{$equipment->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('equipments', ['id' => $equipment->id]);
    }

    // ─── Authorization Tests ──────────────────────────────────

    public function test_guest_cannot_access_equipment_api(): void
    {
        $this->getJson('/api/v1/equipments')
            ->assertStatus(401);
    }

    public function test_user_without_permission_cannot_create_equipment(): void
    {
        $response = $this->actingAs($this->userNoAccess, 'sanctum')
            ->postJson('/api/v1/equipments', [
                'equipment_code' => 'EQP-999',
                'equipment_name' => 'Unauthorized',
                'status' => 'active',
            ]);

        $response->assertStatus(403);
    }

    // ─── Validation Tests ─────────────────────────────────────

    public function test_equipment_code_is_required(): void
    {
        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/v1/equipments', [
                'equipment_name' => 'Test',
                'status' => 'active',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['equipment_code']);
    }

    public function test_equipment_code_must_be_unique(): void
    {
        Equipment::create([
            'equipment_code' => 'EQP-DUPE',
            'equipment_name' => 'First',
            'status' => 'active',
            'created_by' => $this->admin->id,
            'updated_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/v1/equipments', [
                'equipment_code' => 'EQP-DUPE',
                'equipment_name' => 'Second',
                'status' => 'active',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['equipment_code']);
    }

    public function test_status_must_be_valid_value(): void
    {
        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/v1/equipments', [
                'equipment_code' => 'EQP-STATUS',
                'equipment_name' => 'Test',
                'status' => 'invalid_status',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    }
}
