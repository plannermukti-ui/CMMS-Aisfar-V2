<?php

namespace App\Filament\Resources\Vendor\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class VendorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->label('Kode Vendor')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                TextInput::make('name')
                    ->label('Nama Vendor / Supplier')
                    ->required()
                    ->maxLength(255),
                TextInput::make('contact_person')
                    ->label('Contact Person')
                    ->maxLength(255),
                TextInput::make('phone')
                    ->label('No. Telepon / WhatsApp')
                    ->tel()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('Email Perusahaan')
                    ->email()
                    ->maxLength(255),
                TextInput::make('npwp')
                    ->label('NPWP (Nomor Pokok Wajib Pajak)')
                    ->maxLength(255),
                Select::make('term_of_payment')
                    ->label('Term of Payment (TOP)')
                    ->options([
                        'CBD' => 'Cash Before Delivery (CBD)',
                        'COD' => 'Cash on Delivery (COD)',
                        'Net 14' => 'Net 14 Hari',
                        'Net 30' => 'Net 30 Hari',
                        'Net 60' => 'Net 60 Hari',
                    ])
                    ->default('Net 30')
                    ->required(),
                TextInput::make('bank_name')
                    ->label('Nama Bank')
                    ->placeholder('Contoh: BCA / Mandiri / BNI'),
                TextInput::make('bank_account_number')
                    ->label('Nomor Rekening Bank'),
                TextInput::make('bank_account_holder')
                    ->label('Nama Pemilik Rekening'),
                Textarea::make('address')
                    ->label('Alamat Kantor / Gudang Vendor')
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->label('Status Aktif')
                    ->default(true),
            ])
            ->columns(['default' => 1, 'lg' => 2]);
    }
}
