<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CustomEditProfile extends BaseEditProfile
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Informasi Akun')
                    ->description('Pengaturan kredensial dan email akun Anda.')
                    ->schema([
                        $this->getNameFormComponent(),
                        $this->getEmailFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                    ])->columns(2),

                Section::make('Informasi Perusahaan')
                    ->description('Data kepegawaian. Jika terdapat kesalahan, mohon hubungi Administrator/HRD.')
                    ->schema([
                        Select::make('department_id')
                            ->label('Departemen')
                            ->relationship('department', 'name')
                            ->disabled()
                            ->dehydrated(false),

                        Select::make('position_id')
                            ->label('Jabatan')
                            ->relationship('position', 'name')
                            ->disabled()
                            ->dehydrated(false),

                        TextInput::make('nik')
                            ->label('NIK (Nomor Induk Karyawan)')
                            ->disabled()
                            ->dehydrated(false),

                        TextInput::make('join_year')
                            ->label('Tahun Bergabung')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(false),
                    ])->columns(2),

                Section::make('Biodata Karyawan')
                    ->description('Lengkapi biodata diri Anda untuk keperluan data internal.')
                    ->schema([
                        DatePicker::make('date_of_birth')
                            ->label('Tanggal Lahir Lengkap')
                            ->native(false)
                            ->displayFormat('d F Y'),

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
