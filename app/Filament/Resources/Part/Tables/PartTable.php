<?php

namespace App\Filament\Resources\Part\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class PartTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('part_number')->label('Part Number')->searchable()->sortable()->weight('bold'),
                TextColumn::make('name')->label('Nama Suku Cadang')->searchable()->sortable(),
                TextColumn::make('category')->label('Kategori')->badge(),
                TextColumn::make('stock_on_hand')
                    ->label('Stok Gudang')
                    ->sortable()
                    ->badge()
                    ->color(fn ($record) => $record->stock_on_hand <= 0 ? 'danger' : ($record->stock_on_hand <= $record->min_stock ? 'warning' : 'success'))
                    ->formatStateUsing(fn ($record) => $record->stock_on_hand.' '.$record->uom),
                TextColumn::make('bin_location')->label('Lokasi Rak'),
                TextColumn::make('standard_cost')->label('Harga Standar')->money('IDR')->sortable(),
                IconColumn::make('is_active')->label('Aktif')->boolean(),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->options([
                        'Filter' => 'Filter',
                        'Hydraulic' => 'Hydraulic',
                        'Engine' => 'Engine',
                        'Undercarriage' => 'Undercarriage',
                        'Electrical' => 'Electrical',
                        'Lubricant' => 'Lubricant',
                        'General Hardware' => 'General Hardware',
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
