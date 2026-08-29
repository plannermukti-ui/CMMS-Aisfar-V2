<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserCrudTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'PermissionSeeder']);
        $this->artisan('db:seed', ['--class' => 'UserSeeder']);
    }

    public function test_can_list_users_with_permission()
    {
        $adminUser = User::where('username', 'admin')->first();

        $response = $this->actingAs($adminUser, 'sanctum')->getJson('/api/v1/users');

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => [['id', 'username', 'email']]]);
    }

    public function test_cannot_list_users_without_permission()
    {
        $user = User::factory()->create(['status' => 'active']); // No roles

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/v1/users');

        $response->assertStatus(403);
    }

    public function test_can_create_user()
    {
        $adminUser = User::where('username', 'admin')->first();
        $guestRole = Role::where('name', 'guest')->first();

        $data = [
            'username' => 'newuser',
            'full_name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'status' => 'pending',
            'roles' => [$guestRole->id],
        ];

        $response = $this->actingAs($adminUser, 'sanctum')->postJson('/api/v1/users', $data);

        $response->assertStatus(201)
            ->assertJsonFragment(['username' => 'newuser', 'status' => 'pending']);

        $this->assertDatabaseHas('users', ['email' => 'newuser@example.com']);
    }

    public function test_can_update_user_status()
    {
        $adminUser = User::where('username', 'admin')->first();
        $userToUpdate = User::factory()->create(['status' => 'pending']);

        $data = [
            'status' => 'active',
        ];

        $response = $this->actingAs($adminUser, 'sanctum')->putJson("/api/v1/users/{$userToUpdate->id}", $data);

        $response->assertStatus(200)
            ->assertJsonFragment(['status' => 'active']);

        $this->assertDatabaseHas('users', ['id' => $userToUpdate->id, 'status' => 'active']);
    }
}
