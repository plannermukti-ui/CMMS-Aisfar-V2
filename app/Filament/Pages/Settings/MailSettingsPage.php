<?php

namespace App\Filament\Pages\Settings;

use App\Filament\Clusters\Settings\SettingsCluster;
use App\Settings\MailSettings;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MailSettingsPage extends SettingsPage
{
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationLabel = 'Konfigurasi Email';

    protected static ?string $title = 'Pengaturan SMTP Email';

    protected static ?string $cluster = SettingsCluster::class;

    protected static string $settings = MailSettings::class;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Kredensial SMTP')
                    ->description('Konfigurasi pengiriman email aplikasi.')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('mail_mailer')
                                ->label('Mailer')
                                ->options([
                                    'smtp' => 'SMTP',
                                    'sendmail' => 'Sendmail',
                                    'log' => 'Log (Testing)',
                                ])
                                ->default('smtp')
                                ->required(),

                            TextInput::make('mail_host')
                                ->label('Mail Host')
                                ->placeholder('smtp.googlemail.com')
                                ->required(),

                            TextInput::make('mail_port')
                                ->label('Mail Port')
                                ->numeric()
                                ->placeholder('465')
                                ->required(),

                            Select::make('mail_encryption')
                                ->label('Mail Encryption')
                                ->options([
                                    'tls' => 'TLS',
                                    'ssl' => 'SSL',
                                    '' => 'None',
                                ])
                                ->required(),

                            TextInput::make('mail_username')
                                ->label('Mail Username')
                                ->required(),

                            TextInput::make('mail_password')
                                ->label('Mail Password')
                                ->password()
                                ->revealable()
                                ->required(),
                        ]),

                        Grid::make(2)->schema([
                            TextInput::make('mail_from_address')
                                ->label('Mail From Address')
                                ->email()
                                ->placeholder('no-reply@perusahaan.com')
                                ->required(),

                            TextInput::make('mail_from_name')
                                ->label('Mail From Name')
                                ->placeholder('Sistem CMMS')
                                ->required(),
                        ]),
                    ]),
            ]);
    }
}
