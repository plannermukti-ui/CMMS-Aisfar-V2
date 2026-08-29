<?php

namespace App\Filament\Resources\Sites\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SiteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('site_code')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                TextInput::make('site_name')
                    ->required()
                    ->maxLength(255),
                Textarea::make('address')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Textarea::make('remarks')
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ])
            ->columns(['default' => 1, 'lg' => 2]);
    }
}
