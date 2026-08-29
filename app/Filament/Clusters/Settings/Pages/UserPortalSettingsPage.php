<?php

namespace App\Filament\Clusters\Settings\Pages;

use App\Filament\Clusters\Settings\SettingsCluster;
use App\Settings\UserPortalSettings;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;

class UserPortalSettingsPage extends SettingsPage
{
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-computer-desktop';

    protected static ?string $navigationLabel = 'Portal Karyawan';

    protected static ?string $title = 'Pengaturan Portal Karyawan (Metronic)';

    protected static string $settings = UserPortalSettings::class;

    protected static ?string $cluster = SettingsCluster::class;

    public function getMaxContentWidth(): Width|string|null
    {
        return Width::Full;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(['default' => 1, 'lg' => 2])
            ->components([
                Section::make('Layout Utama')
                    ->description('Atur struktur navigasi utama Portal Karyawan.')
                    ->icon('heroicon-o-rectangle-group')
                    ->columns(['default' => 1, 'sm' => 2])
                    ->schema([
                        Select::make('layout_type')
                            ->label('Tipe Tata Letak')
                            ->options([
                                'header' => 'Header Saja (Menu Sedikit)',
                                'sidebar' => 'Sidebar Kiri (Menu Kompleks)',
                            ])
                            ->required(),
                        Select::make('theme_mode')
                            ->label('Mode Gelap / Terang')
                            ->options([
                                'light' => 'Terang (Light Mode)',
                                'dark' => 'Gelap (Dark Mode)',
                                'system' => 'Otomatis mengikuti Sistem',
                            ])
                            ->required(),
                        Select::make('container_width')
                            ->label('Lebar Kontainer (Container Width)')
                            ->options([
                                'fluid' => 'Penuh (Fluid - 100%)',
                                'fixed' => 'Terpusat (Fixed)',
                            ])
                            ->required(),
                        ColorPicker::make('primary_color')
                            ->label('Warna Utama (Primary Color)')
                            ->default('#009EF7')
                            ->required(),
                    ])
                    ->columnSpan(['default' => 1, 'lg' => 1]),

                Section::make('Tipografi & Gaya Visual')
                    ->description('Sesuaikan gaya teks, form, dan ikon yang digunakan di Metronic.')
                    ->icon('heroicon-o-paint-brush')
                    ->columns(['default' => 1, 'sm' => 2])
                    ->schema([
                        Select::make('font_family')
                            ->label('Jenis Huruf (Font)')
                            ->options([
                                'Inter' => 'Inter (Bawaan Metronic)',
                                'Roboto' => 'Roboto',
                                'Poppins' => 'Poppins',
                            ])
                            ->required(),
                        Select::make('form_style')
                            ->label('Gaya Formulir (Form Style)')
                            ->options([
                                'default' => 'Garis Luar (Outline)',
                                'solid' => 'Padat (Solid Background)',
                                'transparent' => 'Transparan',
                            ])
                            ->required(),
                        Select::make('button_style')
                            ->label('Gaya Tombol (Button Style)')
                            ->options([
                                'default' => 'Standar (Warna Penuh)',
                                'light' => 'Terang (Warna Latar Pudar)',
                                'outline' => 'Garis Luar Saja',
                            ])
                            ->required(),
                        Select::make('menu_icon_style')
                            ->label('Gaya Ikon Menu')
                            ->options([
                                'duotune' => 'Duotune (Ikon 2 warna)',
                                'outline' => 'Outline (Garis Luar)',
                                'solid' => 'Solid (Warna Penuh)',
                            ])
                            ->required(),
                    ])
                    ->columnSpan(['default' => 1, 'lg' => 1]),

                Section::make('Navigasi & Elemen')
                    ->description('Konfigurasi elemen header, sidebar, dan toolbar.')
                    ->icon('heroicon-o-adjustments-horizontal')
                    ->columns(['default' => 1, 'sm' => 2, 'md' => 3])
                    ->schema([
                        Toggle::make('header_fixed')
                            ->label('Header Tetap (Fixed Header)')
                            ->helperText('Header melayang di atas.'),
                        Toggle::make('sidebar_fixed')
                            ->label('Sidebar Tetap (Fixed Sidebar)')
                            ->helperText('Sidebar tidak ikut tergulir.'),
                        Toggle::make('toolbar_display')
                            ->label('Tampilkan Toolbar')
                            ->helperText('Tampilkan judul halaman.'),
                        Toggle::make('toolbar_fixed')
                            ->label('Toolbar Tetap')
                            ->helperText('Toolbar akan melayang di atas.'),
                        Toggle::make('footer_display')
                            ->label('Tampilkan Footer')
                            ->helperText('Menampilkan hak cipta bawah.'),
                        Toggle::make('footer_fixed')
                            ->label('Footer Tetap')
                            ->helperText('Footer selalu tampak.'),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
