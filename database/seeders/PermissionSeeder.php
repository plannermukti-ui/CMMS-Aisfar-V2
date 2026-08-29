<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Equipment
            ['category' => 'equipment', 'action' => 'view', 'display_name' => 'View Equipment'],
            ['category' => 'equipment', 'action' => 'create', 'display_name' => 'Create Equipment'],
            ['category' => 'equipment', 'action' => 'edit', 'display_name' => 'Edit Equipment'],
            ['category' => 'equipment', 'action' => 'delete', 'display_name' => 'Delete Equipment'],
            ['category' => 'equipment', 'action' => 'export', 'display_name' => 'Export Equipment'],

            // Work Order
            ['category' => 'work-order', 'action' => 'view', 'display_name' => 'View Work Orders'],
            ['category' => 'work-order', 'action' => 'create', 'display_name' => 'Create Work Order'],
            ['category' => 'work-order', 'action' => 'edit', 'display_name' => 'Edit Work Order'],
            ['category' => 'work-order', 'action' => 'approve', 'display_name' => 'Approve Work Order'],
            ['category' => 'work-order', 'action' => 'assign', 'display_name' => 'Assign Technician'],

            // User
            ['category' => 'user', 'action' => 'view', 'display_name' => 'View Users'],
            ['category' => 'user', 'action' => 'create', 'display_name' => 'Create User'],
            ['category' => 'user', 'action' => 'edit', 'display_name' => 'Edit User'],
            ['category' => 'user', 'action' => 'delete', 'display_name' => 'Delete User'],
            ['category' => 'user', 'action' => 'approve', 'display_name' => 'Approve User'],

            // Role
            ['category' => 'role', 'action' => 'view', 'display_name' => 'View Roles'],
            ['category' => 'role', 'action' => 'create', 'display_name' => 'Create Role'],
            ['category' => 'role', 'action' => 'edit', 'display_name' => 'Edit Role'],
            ['category' => 'role', 'action' => 'delete', 'display_name' => 'Delete Role'],

            // Message
            ['category' => 'message', 'action' => 'view', 'display_name' => 'View Messages'],
            ['category' => 'message', 'action' => 'create', 'display_name' => 'Create Message'],
            ['category' => 'message', 'action' => 'delete', 'display_name' => 'Delete Message'],

            // System (Admin Only)
            ['category' => 'system', 'action' => 'view-audit', 'display_name' => 'View Audit Log'],
            ['category' => 'settings', 'action' => 'view', 'display_name' => 'View Settings'],
            ['category' => 'settings', 'action' => 'edit', 'display_name' => 'Edit Settings'],
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(
                ['name' => "{$perm['action']} {$perm['category']}"],
                [
                    'display_name' => $perm['display_name'],
                    'category' => $perm['category'],
                    'action' => $perm['action'],
                ]
            );
        }
    }
}
