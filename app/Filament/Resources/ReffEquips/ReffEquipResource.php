<?php

namespace App\Filament\Resources\ReffEquips;

use App\Filament\Resources\ReffEquips\Pages\CreateReffEquip;
use App\Filament\Resources\ReffEquips\Pages\EditReffEquip;
use App\Filament\Resources\ReffEquips\Pages\ListReffEquips;
use App\Filament\Resources\ReffEquips\Schemas\ReffEquipForm;
use App\Filament\Resources\ReffEquips\Tables\ReffEquipsTable;
use App\Models\ReffEquip;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReffEquipResource extends Resource
{
    protected static ?string $model = ReffEquip::class;

    protected static \UnitEnum|string|null $navigationGroup = 'Setting Database';

    protected static ?string $navigationLabel = 'Setting Equip';

    protected static ?string $modelLabel = 'Setting Equip';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return ReffEquipForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ReffEquipsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReffEquips::route('/'),
            'create' => CreateReffEquip::route('/create'),
            'edit' => EditReffEquip::route('/{record}/edit'),
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
