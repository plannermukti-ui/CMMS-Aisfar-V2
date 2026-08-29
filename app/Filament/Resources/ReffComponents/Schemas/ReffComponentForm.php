<?php

namespace App\Filament\Resources\ReffComponents\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ReffComponentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->required()
                    ->maxLength(50)
                    ->unique(ignoreRecord: true)
                    ->label('Component Code')
                    ->placeholder('e.g. CMP-ENG, CMP-UC'),
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Component Name')
                    ->placeholder('e.g. Undercarriage, Engine System'),
                Select::make('category')
                    ->options([
                        'Mechanical' => 'Mechanical',
                        'Electrical' => 'Electrical',
                        'Hydraulic' => 'Hydraulic',
                        'Powertrain' => 'Powertrain',
                        'Structure' => 'Structure',
                        'HVAC' => 'HVAC',
                        'Auxiliary' => 'Auxiliary',
                    ])
                    ->default('Mechanical')
                    ->required(),
                Select::make('equipment_types')
                    ->options([
                        'Excavator' => 'Excavator (Tracked)',
                        'Dozer' => 'Dozer (Tracked)',
                        'Off-Highway Dump Truck' => 'Off-Highway Dump Truck (OHT)',
                        'Articulated Dump Truck' => 'Articulated Dump Truck (ADT)',
                        'Wheel Loader' => 'Wheel Loader',
                        'Motor Grader' => 'Motor Grader',
                        'Light Vehicle' => 'Light Vehicle (LV)',
                        'Highway Dump Truck' => 'Highway Dump Truck',
                        'Water Truck' => 'Water Truck',
                        'Fuel Truck' => 'Fuel Truck',
                        'Mobile Crane' => 'Mobile Crane',
                        'Tower Lamp' => 'Tower Lamp',
                        'Generator Set' => 'Generator Set',
                    ])
                    ->multiple()
                    ->searchable()
                    ->helperText('Biarkan kosong jika berlaku untuk SEMUA tipe equipment.'),
                TextInput::make('sort_order')
                    ->numeric()
                    ->default(1),
                Select::make('status')
                    ->options([
                        'Active' => 'Active',
                        'Inactive' => 'Inactive',
                    ])
                    ->default('Active')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull()
                    ->rows(3),
            ])
            ->columns(['default' => 1, 'lg' => 2]);
    }
}
