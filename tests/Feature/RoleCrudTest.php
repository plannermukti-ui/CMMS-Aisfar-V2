<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RoleCrudTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure default permissions exist
        $this->artisan('db:seed', ['--class' => 'PermissionSeeder']);
        $this->artisan('db:seed', ['--class' => 'UserSeeder']);
    }

    public function test_can_list_roles_with_permission()
    {
        $user = User::factory()->create(['status' => 'active']);
        $role = Role::where('name', 'admin')->first();
        $user->roles()->attach($role->id);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/v1/roles');

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => [['id', 'name']]]);
    }

    public function test_cannot_list_roles_without_permission()
    {
        $user = User::factory()->create(['status' => 'active']); // No roles

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/v1/roles');

        $response->assertStatus(403);
    }

    public function test_can_create_role()
    {
        $user = User::factory()->create(['status' => 'active']);
        $adminRole = Role::where('name', 'admin')->first();
        $user->roles()->attach($adminRole->id);

        $permission = Permission::first();

        $data = [
            'name' => 'manager',
            'display_name' => 'Manager',
            'description' => 'A manager role',
            'permissions' => [$permission->id],
        ];

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/roles', $data);
        $response->dump();

        $response->assertStatus(201)
            ->assertJsonFragment(['name' => 'manager']);

        $this->assertDatabaseHas('roles', ['name' => 'manager']);
    }
}
