<?php

namespace App\Filament\Resources\ReffComponents\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ReffComponentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('category')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Powertrain' => 'danger',
                        'Hydraulic' => 'info',
                        'Electrical' => 'warning',
                        'Structure' => 'success',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('equipment_types')
                    ->label('Applicable To')
                    ->badge()
                    ->getStateUsing(fn ($record) => empty($record->equipment_types) ? 'All Equipment' : implode(', ', $record->equipment_types))
                    ->wrap(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'Active' ? 'success' : 'danger'),
                TextColumn::make('sort_order')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->options([
                        'Mechanical' => 'Mechanical',
                        'Electrical' => 'Electrical',
                        'Hydraulic' => 'Hydraulic',
                        'Powertrain' => 'Powertrain',
                        'Structure' => 'Structure',
                        'HVAC' => 'HVAC',
                        'Auxiliary' => 'Auxiliary',
                    ]),
                TrashedFilter::make(),
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
