<?php

namespace App\Filament\Resources\Vendor;

use App\Filament\Resources\Vendor\Pages\CreateVendor;
use App\Filament\Resources\Vendor\Pages\EditVendor;
use App\Filament\Resources\Vendor\Pages\ListVendors;
use App\Filament\Resources\Vendor\Schemas\VendorForm;
use App\Filament\Resources\Vendor\Tables\VendorTable;
use App\Models\Vendor;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VendorResource extends Resource
{
    protected static ?string $model = Vendor::class;

    protected static \UnitEnum|string|null $navigationGroup = 'Supply Chain & SCM';

    protected static ?string $navigationLabel = 'Master Vendors';

    protected static ?string $modelLabel = 'Vendor';

    protected static ?string $pluralModelLabel = 'Master Vendors';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingStorefront;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return VendorForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VendorTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVendors::route('/'),
            'create' => CreateVendor::route('/create'),
            'edit' => EditVendor::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
