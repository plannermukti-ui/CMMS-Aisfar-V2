<?php

namespace App\Filament\Pages\Settings;

use App\Filament\Clusters\Settings\SettingsCluster;
use App\Settings\GeneralSettings;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class GeneralSettingsPage extends SettingsPage
{
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationLabel = 'Umum (General)';

    protected static ?string $title = 'Pengaturan Umum';

    protected static ?string $cluster = SettingsCluster::class;

    protected static string $settings = GeneralSettings::class;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Perusahaan')
                    ->description('Pengaturan dasar profil perusahaan Anda.')
                    ->schema([
                        FileUpload::make('site_logo')
                            ->label('Logo Perusahaan')
                            ->image()
                            ->disk('public')
                            ->visibility('public')
                            ->directory('settings/logo')
                            ->openable()
                            ->downloadable()
                            ->deletable(true)
                            ->maxSize(2048)
                            ->columnSpanFull(),

                        TextInput::make('site_name')
                            ->label('Nama Perusahaan')
                            ->required()
                            ->maxLength(255),

                        Textarea::make('company_address')
                            ->label('Alamat Lengkap')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(1),
            ]);
    }
}
