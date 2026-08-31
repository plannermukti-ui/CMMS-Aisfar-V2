<?php

namespace App\Filament\Resources\PmUnitModels\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class PmUnitModelsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reffEquip.model')
                    ->label('Equipment Model')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->placeholder('-'),
                TextColumn::make('name')
                    ->label('PM Model Name')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),
                TextColumn::make('measurement_type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => $state === 'hm' ? 'Hour Meter (HM)' : 'Kilometer (KM)')
                    ->color(fn (string $state): string => $state === 'hm' ? 'primary' : 'info'),
                TextColumn::make('target_usage_per_day')
                    ->label('Usage/Day')
                    ->numeric(1)
                    ->sortable()
                    ->suffix(fn (string $state, $record): string => $record->measurement_type === 'hm' ? ' hrs' : ' km'),
                TextColumn::make('remarks')
                    ->label('Remarks')
                    ->limit(50)
                    ->tooltip(fn ($record): string => $record->remarks ?? '-'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'active' ? 'success' : 'danger'),
                TextColumn::make('equipments_count')
                    ->counts('equipments')
                    ->label('Units')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('name')
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ]),
                SelectFilter::make('measurement_type')
                    ->label('Measurement Type')
                    ->options([
                        'hm' => 'Hour Meter (HM)',
                        'km' => 'Kilometer (KM)',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
