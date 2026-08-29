<?php

namespace App\Filament\Resources\Part\Schemas;

use App\Models\Site;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PartForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Utama Suku Cadang')
                    ->schema([
                        TextInput::make('part_number')
                            ->label('Part Number')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        TextInput::make('name')
                            ->label('Part Name')
                            ->required()
                            ->maxLength(255),
                        Select::make('category')
                            ->options([
                                'Filter' => 'Filter',
                                'Hydraulic' => 'Hydraulic',
                                'Engine' => 'Engine',
                                'Undercarriage' => 'Undercarriage',
                                'Electrical' => 'Electrical',
                                'Lubricant' => 'Lubricant',
                                'General Hardware' => 'General Hardware',
                            ])
                            ->default('Filter')
                            ->required(),
                        Select::make('uom')
                            ->label('Satuan (UOM)')
                            ->options([
                                'Pcs' => 'Pcs',
                                'Set' => 'Set',
                                'Liter' => 'Liter',
                                'Drum' => 'Drum',
                                'Meter' => 'Meter',
                                'Box' => 'Box',
                            ])
                            ->default('Pcs')
                            ->required(),
                        TextInput::make('stock_on_hand')
                            ->label('Total Stok Fisik Gudang')
                            ->numeric()
                            ->default(0)
                            ->required(),
                        TextInput::make('min_stock')
                            ->label('Batas Minimum Stok')
                            ->numeric()
                            ->default(1)
                            ->required(),
                        TextInput::make('standard_cost')
                            ->label('Harga Standar (IDR)')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0),
                        Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->default(true),
                    ])
                    ->columns(['default' => 1, 'lg' => 2]),

                Section::make('Daftar Lokasi Penempatan & Rak Gudang (Multi-Site & Multi-Rak)')
                    ->description('Tentukan penempatan fisik rak atau bin suku cadang ini di setiap site operasional atau gudang.')
                    ->schema([
                        Repeater::make('locations')
                            ->relationship('locations')
                            ->schema([
                                Select::make('site_id')
                                    ->label('Site Operasional')
                                    ->options(fn () => Site::pluck('site_name', 'id'))
                                    ->searchable()
                                    ->nullable(),
                                TextInput::make('warehouse_name')
                                    ->label('Nama Gudang / Workshop')
                                    ->default('Gudang Utama')
                                    ->required(),
                                TextInput::make('rack_location')
                                    ->label('Kode Rak / Bin / Tingkat')
                                    ->placeholder('Contoh: Rak A-02 / Bin 14')
                                    ->required(),
                                TextInput::make('stock_qty')
                                    ->label('Stok di Rak Ini')
                                    ->numeric()
                                    ->default(0),
                                TextInput::make('notes')
                                    ->label('Keterangan / Posisi')
                                    ->placeholder('Contoh: Rak Utama, Rak Cadangan'),
                                Toggle::make('is_primary')
                                    ->label('Lokasi Utama')
                                    ->default(true),
                            ])
                            ->columns(['default' => 1, 'md' => 3])
                            ->defaultItems(1)
                            ->addActionLabel('+ Tambah Lokasi / Rak Baru'),
                    ]),
            ]);
    }
}
