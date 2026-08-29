<?php

namespace App\Filament\Resources\Vendor\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class VendorTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->label('Kode')->searchable()->sortable()->weight('bold'),
                TextColumn::make('name')->label('Nama Vendor / Supplier')->searchable()->sortable(),
                TextColumn::make('contact_person')->label('Contact Person'),
                TextColumn::make('phone')->label('Telepon')->searchable(),
                TextColumn::make('term_of_payment')->label('TOP')->badge(),
                TextColumn::make('npwp')->label('NPWP'),
                IconColumn::make('is_active')->label('Aktif')->boolean(),
            ])
            ->filters([
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
