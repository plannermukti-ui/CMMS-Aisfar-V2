<?php

namespace Tests\Feature;

use App\Models\Message;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MessageCrudTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PermissionSeeder::class);

        $this->adminRole = Role::create(['name' => 'admin', 'display_name' => 'Admin']);
        $this->adminRole->permissions()->attach(Permission::where('category', 'message')->pluck('id'));

        $this->user = User::factory()->create();
        $this->user->roles()->attach($this->adminRole);

        $this->otherUser = User::factory()->create();
    }

    public function test_can_list_messages()
    {
        Message::create([
            'sender_id' => $this->user->id,
            'receiver_id' => $this->otherUser->id,
            'message' => 'Hello there',
        ]);

        $response = $this->actingAs($this->user)->getJson('/api/v1/messages');

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['data' => [['id', 'sender_id', 'receiver_id', 'message']]]]);
    }

    public function test_can_send_message()
    {
        $response = $this->actingAs($this->user)->postJson('/api/v1/messages', [
            'receiver_id' => $this->otherUser->id,
            'message' => 'Test message',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.message', 'Test message');

        $this->assertDatabaseHas('messages', [
            'sender_id' => $this->user->id,
            'receiver_id' => $this->otherUser->id,
            'message' => 'Test message',
        ]);
    }

    public function test_cannot_read_others_messages()
    {
        $thirdUser = User::factory()->create();

        $message = Message::create([
            'sender_id' => $this->otherUser->id,
            'receiver_id' => $thirdUser->id,
            'message' => 'Secret',
        ]);

        $response = $this->actingAs($this->user)->getJson("/api/v1/messages/{$message->id}");

        $response->assertStatus(403);
    }
}
