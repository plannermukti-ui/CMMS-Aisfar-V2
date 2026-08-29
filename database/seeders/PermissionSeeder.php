<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Modules with their display name and CRUD actions.
     *
     * @var array<string, array<string, mixed>>
     */
    private array $modules = [
        // ─── PLANT MODULE ────────────────────────────────────────────
        'workorder' => [
            'display' => 'Work Order (PLANT)',
            'actions' => ['view', 'create', 'edit', 'delete', 'approve', 'assign'],
        ],
        'component-tracker' => [
            'display' => 'Component Tracker (PLANT)',
            'actions' => ['view', 'create', 'edit', 'delete'],
        ],
        'ccr' => [
            'display' => 'Kondisi Fisik / CCR (PLANT)',
            'actions' => ['view', 'create', 'edit', 'delete'],
        ],
        'far' => [
            'display' => 'Failure Analysis / FAR (PLANT)',
            'actions' => ['view', 'create', 'edit', 'delete'],
        ],
        'osr' => [
            'display' => 'Perbaikan Luar / OSR (PLANT)',
            'actions' => ['view', 'create', 'edit', 'delete'],
        ],
        // ─── SCM MODULE ───────────────────────────────────────────────
        'scm-parts' => [
            'display' => 'Master Parts (SCM)',
            'actions' => ['view', 'create', 'edit', 'delete'],
        ],
        'scm-mol' => [
            'display' => 'Material Order / MOL (SCM)',
            'actions' => ['view', 'create', 'edit', 'delete', 'approve'],
        ],
        'scm-pr' => [
            'display' => 'Purchase Request / PR (SCM)',
            'actions' => ['view', 'create', 'edit', 'delete', 'approve'],
        ],
        'scm-po' => [
            'display' => 'Purchase Order / PO (SCM)',
            'actions' => ['view', 'create', 'edit', 'delete', 'approve'],
        ],
        'scm-rfq' => [
            'display' => 'Request for Quotation / RFQ (SCM)',
            'actions' => ['view', 'create', 'edit', 'delete'],
        ],
        'scm-do' => [
            'display' => 'Delivery Order / DO (SCM)',
            'actions' => ['view', 'create', 'edit', 'delete'],
        ],
        'scm-gr' => [
            'display' => 'Goods Receipt / GR (SCM)',
            'actions' => ['view', 'create', 'edit', 'delete'],
        ],
        'scm-opname' => [
            'display' => 'Stock Opname (SCM)',
            'actions' => ['view', 'create', 'edit', 'delete', 'approve'],
        ],
        // ─── MASTER DATA ─────────────────────────────────────────────
        'equipment' => [
            'display' => 'Master Equipment',
            'actions' => ['view', 'create', 'edit', 'delete'],
        ],
        // ─── SYSTEM & ADMIN ──────────────────────────────────────────
        'user' => [
            'display' => 'User Management',
            'actions' => ['view', 'create', 'edit', 'delete'],
        ],
        'role' => [
            'display' => 'Role & Permission',
            'actions' => ['view', 'create', 'edit', 'delete'],
        ],
        'message' => [
            'display' => 'Chat & Messenger',
            'actions' => ['view', 'create', 'delete'],
        ],
        'activity-log' => [
            'display' => 'Activity Log',
            'actions' => ['view', 'delete'],
        ],
        'settings' => [
            'display' => 'System Settings',
            'actions' => ['view', 'edit'],
        ],
    ];

    public function run(): void
    {
        foreach ($this->modules as $category => $config) {
            foreach ($config['actions'] as $action) {
                $name = "{$action} {$category}";
                $displayName = ucfirst($action).' '.($config['display'] ?? ucwords(str_replace('-', ' ', $category)));

                Permission::firstOrCreate(
                    ['name' => $name],
                    [
                        'display_name' => $displayName,
                        'category' => $category,
                        'action' => $action,
                        'description' => "Allows user to {$action} {$category}",
                    ]
                );
            }
        }

        $this->command->info('✅ '.Permission::count().' permissions seeded across '.count($this->modules).' modules.');
    }
}
