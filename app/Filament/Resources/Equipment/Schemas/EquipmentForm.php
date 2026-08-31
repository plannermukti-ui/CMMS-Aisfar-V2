<?php

namespace App\Filament\Resources\Equipment\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class EquipmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('unit')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                TextInput::make('no')
                    ->numeric(),
                Select::make('status')
                    ->options([
                        'Active' => 'Active',
                        'Deactive' => 'Deactive',
                    ])
                    ->required()
                    ->default('Active'),
                Select::make('reff_equip_id')
                    ->relationship('reffEquip', 'model')
                    ->searchable()
                    ->preload()
                    ->label('Model')
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->model} - {$record->make} - {$record->tipe} ({$record->class})")
                    ->columnSpanFull(),
                TextInput::make('sn_unit')
                    ->maxLength(255),
                TextInput::make('engine_model')
                    ->maxLength(255),
                TextInput::make('sn_engine')
                    ->maxLength(255),
                TextInput::make('eqp_capacity')
                    ->maxLength(255),
                TextInput::make('no_police')
                    ->maxLength(255),
                TextInput::make('attachment')
                    ->maxLength(255),
                TextInput::make('hp_kw')
                    ->maxLength(255),
                TextInput::make('year_build')
                    ->numeric()
                    ->minValue(1900)
                    ->maxValue(2100),
                DatePicker::make('date_receive'),
                Select::make('site_id')
                    ->relationship('site', 'site_name')
                    ->searchable()
                    ->preload()
                    ->label('Location (Site)'),
                Select::make('pm_unit_model_id')
                    ->relationship('pmUnitModel', 'name')
                    ->searchable()
                    ->preload()
                    ->label('PM Unit Model')
                    ->placeholder('Select PM Model (for Preventive Maintenance)'),
                Textarea::make('remarks')
                    ->columnSpanFull(),
            ])
            ->columns(['default' => 1, 'lg' => 2]);
    }
}
