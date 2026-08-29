<?php

namespace App\Filament\Resources\ReffEquips\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ReffEquipForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('no')->numeric()->required(),
                TextInput::make('make')->required()->maxLength(255),
                TextInput::make('tipe')->required()->maxLength(255),
                TextInput::make('class')->required()->maxLength(255),
                TextInput::make('model')->required()->maxLength(255),
            ])
            ->columns(['default' => 1, 'lg' => 2]);
    }
}
