<?php

namespace App\Filament\Resources\ReffComponents;

use App\Filament\Resources\ReffComponents\Pages\CreateReffComponent;
use App\Filament\Resources\ReffComponents\Pages\EditReffComponent;
use App\Filament\Resources\ReffComponents\Pages\ListReffComponents;
use App\Filament\Resources\ReffComponents\Schemas\ReffComponentForm;
use App\Filament\Resources\ReffComponents\Tables\ReffComponentsTable;
use App\Models\ReffComponent;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReffComponentResource extends Resource
{
    protected static ?string $model = ReffComponent::class;

    protected static \UnitEnum|string|null $navigationGroup = 'Setting Database';

    protected static ?string $navigationLabel = 'Setting Component';

    protected static ?string $modelLabel = 'Setting Component';

    protected static ?int $navigationSort = 4;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    public static function form(Schema $schema): Schema
    {
        return ReffComponentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ReffComponentsTable::configure($table);
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
            'index' => ListReffComponents::route('/'),
            'create' => CreateReffComponent::route('/create'),
            'edit' => EditReffComponent::route('/{record}/edit'),
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
