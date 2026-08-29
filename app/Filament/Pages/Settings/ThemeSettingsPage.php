<?php

namespace App\Filament\Pages\Settings;

use App\Filament\Clusters\Settings\SettingsCluster;
use App\Settings\ThemeSettings;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ThemeSettingsPage extends SettingsPage
{
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-paint-brush';

    protected static ?string $navigationLabel = 'Tema & Tampilan';

    protected static ?string $title = 'Pengaturan Tema Panel';

    protected static ?string $cluster = SettingsCluster::class;

    protected static string $settings = ThemeSettings::class;

    public function getRedirectUrl(): ?string
    {
        return static::getUrl();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Palet Warna')
                    ->description('Sesuaikan warna utama aplikasi agar sesuai dengan branding perusahaan Anda.')
                    ->icon('heroicon-o-swatch')
                    ->schema([
                        Select::make('theme_preset')
                            ->label('Pilih Rekomendasi Tema (Otomatis Terapkan)')
                            ->options([
                                'default' => 'Bawaan (Default Amber)',
                                'ocean' => 'Ocean (Biru Laut & Tosca)',
                                'forest' => 'Forest (Hijau Alam)',
                                'monochrome' => 'Monochrome (Putih Bersih)',
                                'midnight' => 'Midnight (Mode Gelap Modern)',
                            ])
                            ->live()
                            ->afterStateUpdated(function ($state, $set) {
                                if ($state === 'default') {
                                    $set('primary_color', '#f59e0b');
                                    $set('secondary_color', '#3b82f6');
                                    $set('sidebar_color', '#1e293b');
                                    $set('header_color', '#ffffff');
                                    $set('body_background_color', '#f3f4f6');
                                } elseif ($state === 'ocean') {
                                    $set('primary_color', '#0ea5e9');
                                    $set('secondary_color', '#14b8a6');
                                    $set('sidebar_color', '#082f49');
                                    $set('header_color', '#ffffff');
                                    $set('body_background_color', '#f0f9ff');
                                } elseif ($state === 'forest') {
                                    $set('primary_color', '#16a34a');
                                    $set('secondary_color', '#eab308');
                                    $set('sidebar_color', '#14532d');
                                    $set('header_color', '#ffffff');
                                    $set('body_background_color', '#f0fdf4');
                                } elseif ($state === 'monochrome') {
                                    $set('primary_color', '#111827');
                                    $set('secondary_color', '#4b5563');
                                    $set('sidebar_color', '#ffffff');
                                    $set('header_color', '#ffffff');
                                    $set('body_background_color', '#f3f4f6');
                                } elseif ($state === 'midnight') {
                                    $set('primary_color', '#6366f1');
                                    $set('secondary_color', '#ec4899');
                                    $set('sidebar_color', '#0f172a');
                                    $set('header_color', '#1e293b');
                                    $set('body_background_color', '#0f172a');
                                }
                                $set('theme_preset', null); // Reset dropdown after selection
                            })
                            ->dehydrated(false)
                            ->columnSpanFull(),

                        Grid::make(2)->schema([
                            ColorPicker::make('primary_color')
                                ->label('Warna Utama (Primary)')
                                ->hex()
                                ->required(),

                            ColorPicker::make('secondary_color')
                                ->label('Warna Sekunder (Secondary)')
                                ->hex()
                                ->required(),

                            ColorPicker::make('sidebar_color')
                                ->label('Warna Sidebar')
                                ->hex()
                                ->required(),

                            ColorPicker::make('header_color')
                                ->label('Warna Header')
                                ->hex()
                                ->required(),

                            ColorPicker::make('body_background_color')
                                ->label('Warna Latar Body')
                                ->hex()
                                ->required(),
                        ]),
                    ]),

                Section::make('Tipografi')
                    ->description('Pilih jenis huruf yang ingin digunakan di seluruh aplikasi.')
                    ->icon('heroicon-o-language')
                    ->schema([
                        Select::make('font_family')
                            ->label('Font Family')
                            ->options([
                                'Inter' => 'Inter (Modern Sans-serif)',
                                'Roboto' => 'Roboto (Google Sans)',
                                'Outfit' => 'Outfit (Geometric Sans)',
                                'Poppins' => 'Poppins (Round Sans)',
                                'Open Sans' => 'Open Sans',
                            ])
                            ->default('Inter')
                            ->required()
                            ->searchable(),
                    ]),
            ]);
    }
}
