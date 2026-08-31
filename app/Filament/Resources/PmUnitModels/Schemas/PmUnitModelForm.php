<?php

namespace App\Filament\Resources\PmUnitModels\Schemas;

use App\Models\ReffEquip;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PmUnitModelForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('reff_equip_id')
                    ->relationship('reffEquip', 'model')
                    ->searchable()
                    ->preload()
                    ->label('Equipment Model')
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->model} - {$record->make} - {$record->tipe} ({$record->class})")
                    ->required()
                    ->columnSpanFull()
                    ->afterStateUpdated(function ($set, $state) {
                        if ($state) {
                            $reffEquip = ReffEquip::find($state);
                            if ($reffEquip) {
                                $set('name', $reffEquip->model);
                            }
                        }
                    }),
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Model Name')
                    ->placeholder('Auto-filled from Equipment Model')
                    ->helperText('Will be auto-filled when you select an Equipment Model, or you can edit it manually.'),
                Select::make('measurement_type')
                    ->options([
                        'hm' => 'Hour Meter (HM)',
                        'km' => 'Kilometer (KM)',
                    ])
                    ->required()
                    ->default('hm')
                    ->label('Measurement Type')
                    ->helperText('HM for hour-based equipment, KM for distance-based'),
                TextInput::make('target_usage_per_day')
                    ->required()
                    ->numeric()
                    ->default(8)
                    ->minValue(0.5)
                    ->maxValue(24)
                    ->step(0.5)
                    ->label('Target Usage Per Day')
                    ->helperText('Estimated hours or km used per day (for schedule planning)'),
                Textarea::make('remarks')
                    ->label('Remarks')
                    ->placeholder('Additional notes about this unit model...')
                    ->columnSpanFull(),
                Select::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ])
                    ->required()
                    ->default('active'),
            ])
            ->columns(['default' => 1, 'lg' => 2]);
    }
}
