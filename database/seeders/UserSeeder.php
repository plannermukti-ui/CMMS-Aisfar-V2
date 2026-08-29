<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::firstOrCreate(
            ['name' => 'admin'],
            ['display_name' => 'System Administrator']
        );

        $guestRole = Role::firstOrCreate(
            ['name' => 'guest'],
            ['display_name' => 'Guest User']
        );

        // Assign all permissions to admin role
        $adminRole->permissions()->sync(Permission::all());

        $adminUser = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'username' => 'admin',
                'full_name' => 'Super Administrator',
                'password' => Hash::make('password123'), // Change in production
                'status' => 'active',
            ]
        );

        $adminUser->roles()->sync([$adminRole->id]);
    }
}
