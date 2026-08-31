<?php

namespace App\Filament\Resources\PmUnitModels;

use App\Filament\Resources\PmUnitModels\Pages\CreatePmUnitModel;
use App\Filament\Resources\PmUnitModels\Pages\EditPmUnitModel;
use App\Filament\Resources\PmUnitModels\Pages\ListPmUnitModels;
use App\Filament\Resources\PmUnitModels\Schemas\PmUnitModelForm;
use App\Filament\Resources\PmUnitModels\Tables\PmUnitModelsTable;
use App\Models\PmUnitModel;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PmUnitModelResource extends Resource
{
    protected static ?string $model = PmUnitModel::class;

    protected static \UnitEnum|string|null $navigationGroup = 'Preventive Maintenance';

    protected static ?string $navigationLabel = 'Unit Models';

    protected static ?string $modelLabel = 'PM Unit Model';

    protected static ?string $pluralModelLabel = 'PM Unit Models';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCpuChip;

    protected static ?int $navigationSort = 10;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return PmUnitModelForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PmUnitModelsTable::configure($table);
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
            'index' => ListPmUnitModels::route('/'),
            'create' => CreatePmUnitModel::route('/create'),
            'edit' => EditPmUnitModel::route('/{record}/edit'),
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
