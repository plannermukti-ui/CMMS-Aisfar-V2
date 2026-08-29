<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Akun')
                    ->schema([
                        TextInput::make('username')
                            ->required()
                            ->unique(ignoreRecord: true),
                        TextInput::make('full_name')
                            ->required(),
                        TextInput::make('email')
                            ->label('Email address')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true),
                        TextInput::make('password')
                            ->password()
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $operation): bool => $operation === 'create'),
                        Select::make('roles')
                            ->multiple()
                            ->relationship('roles', 'display_name')
                            ->preload()
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Informasi Perusahaan')
                    ->schema([
                        Select::make('department_id')
                            ->label('Departemen')
                            ->relationship('department', 'name', fn (Builder $query) => $query->where('type', 'department')),
                        Select::make('position_id')
                            ->label('Jabatan')
                            ->relationship('position', 'name', fn (Builder $query) => $query->where('type', 'position')),
                        TextInput::make('nik')
                            ->label('NIK (Nomor Induk Karyawan)'),
                        TextInput::make('join_year')
                            ->label('Tahun Bergabung')
                            ->numeric(),
                        Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'active' => 'Active',
                                'suspended' => 'Suspended',
                            ])
                            ->required()
                            ->default('pending'),
                        CheckboxList::make('allowed_modules')
                            ->label('Modul yang Diizinkan')
                            ->options([
                                'admin' => 'Admin Panel (Setting)',
                                'plt' => 'PLANT Maintenance',
                                'scm' => 'SCM Logistics',
                            ])
                            ->columns(3)
                            ->columnSpanFull()
                            ->helperText('Pilih modul mana saja yang bisa diakses oleh user ini.'),
                    ])->columns(2),

                Section::make('Biodata Karyawan')
                    ->schema([
                        DatePicker::make('date_of_birth')
                            ->label('Tanggal Lahir'),
                        TextInput::make('phone')
                            ->label('Nomor Telepon')
                            ->tel(),
                        Radio::make('gender')
                            ->label('Jenis Kelamin')
                            ->options([
                                'L' => 'Laki-Laki',
                                'P' => 'Perempuan',
                            ])
                            ->inline(),
                        Textarea::make('address')
                            ->label('Alamat Lengkap')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }
}
