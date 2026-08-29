<?php

namespace App\Filament\Resources\Roles\Schemas;

use App\Models\Permission;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Collection;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Role Key')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->helperText('e.g. admin, mechanic, supervisor — gunakan huruf kecil'),

                TextInput::make('display_name')
                    ->label('Nama Role')
                    ->required(),

                Textarea::make('description')
                    ->label('Deskripsi Role')
                    ->columnSpanFull(),

                Section::make('🔐 Hak Akses per Modul (CRUD)')
                    ->description('Pilih aksi yang diperbolehkan untuk setiap modul sistem')
                    ->columnSpanFull()
                    ->schema(self::buildPermissionSections()),
            ]);
    }

    /** Build one CheckboxList per permission category (module). */
    private static function buildPermissionSections(): array
    {
        /** @var Collection<string, Collection> $grouped */
        $grouped = Permission::orderBy('category')->orderBy('action')
            ->get()
            ->groupBy('category');

        $moduleLabels = [
            'workorder' => '📋 Work Order (PLANT)',
            'component-tracker' => '⚙️ Component Tracker (PLANT)',
            'ccr' => '📊 Kondisi Fisik / CCR (PLANT)',
            'far' => '🔍 Failure Analysis / FAR (PLANT)',
            'osr' => '🔧 Perbaikan Luar / OSR (PLANT)',
            'scm-parts' => '📦 Master Parts (SCM)',
            'scm-mol' => '🛒 Material Order / MOL (SCM)',
            'scm-pr' => '📝 Purchase Request / PR (SCM)',
            'scm-po' => '🏭 Purchase Order / PO (SCM)',
            'scm-rfq' => '💬 Request for Quotation / RFQ (SCM)',
            'scm-do' => '🚚 Delivery Order / DO (SCM)',
            'scm-gr' => '✅ Goods Receipt / GR (SCM)',
            'scm-opname' => '📊 Stock Opname (SCM)',
            'equipment' => '🚜 Master Equipment',
            'user' => '👤 User Management',
            'role' => '🔐 Role & Permission',
            'message' => '💬 Chat & Messenger',
            'activity-log' => '📖 Activity Log',
            'settings' => '⚙️ System Settings',
        ];

        $actionOrder = ['view' => 0, 'create' => 1, 'edit' => 2, 'delete' => 3, 'approve' => 4, 'assign' => 5];

        $sections = [];

        foreach ($grouped as $category => $permissions) {
            $label = $moduleLabels[$category] ?? ucwords(str_replace('-', ' ', $category));

            $options = $permissions
                ->sortBy(fn ($p) => $actionOrder[$p->action] ?? 99)
                ->mapWithKeys(fn ($p) => [$p->id => ucfirst($p->action)])
                ->toArray();

            $sections[] = Section::make($label)
                ->columnSpanFull()
                ->schema([
                    CheckboxList::make('permissions')
                        ->label('')
                        ->relationship('permissions', 'display_name')
                        ->options($options)
                        ->columns(4)
                        ->bulkToggleable()
                        ->gridDirection('row'),
                ]);
        }

        return $sections;
    }
}
