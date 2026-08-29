<?php

namespace App\Filament\Resources\Part;

use App\Filament\Resources\Part\Pages\CreatePart;
use App\Filament\Resources\Part\Pages\EditPart;
use App\Filament\Resources\Part\Pages\ListParts;
use App\Filament\Resources\Part\Schemas\PartForm;
use App\Filament\Resources\Part\Tables\PartTable;
use App\Models\Part;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PartResource extends Resource
{
    protected static ?string $model = Part::class;

    protected static \UnitEnum|string|null $navigationGroup = 'Supply Chain & SCM';

    protected static ?string $navigationLabel = 'Master Spareparts';

    protected static ?string $modelLabel = 'Sparepart';

    protected static ?string $pluralModelLabel = 'Master Spareparts';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCube;

    protected static ?string $recordTitleAttribute = 'part_number';

    public static function form(Schema $schema): Schema
    {
        return PartForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PartTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListParts::route('/'),
            'create' => CreatePart::route('/create'),
            'edit' => EditPart::route('/{record}/edit'),
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
