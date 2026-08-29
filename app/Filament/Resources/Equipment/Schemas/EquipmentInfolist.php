<?php

namespace App\Filament\Resources\Equipment\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class EquipmentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('unit'),
                TextEntry::make('no'),
                TextEntry::make('status'),
                TextEntry::make('reffEquip.model')->label('Model'),
                TextEntry::make('reffEquip.make')->label('Make'),
                TextEntry::make('reffEquip.tipe')->label('Tipe'),
                TextEntry::make('reffEquip.class')->label('Class'),
                TextEntry::make('sn_unit'),
                TextEntry::make('engine_model'),
                TextEntry::make('sn_engine'),
                TextEntry::make('eqp_capacity'),
                TextEntry::make('no_police'),
                TextEntry::make('attachment'),
                TextEntry::make('hp_kw'),
                TextEntry::make('year_build'),
                TextEntry::make('date_receive')->date(),
                TextEntry::make('site.site_name')->label('Location'),
                TextEntry::make('remarks')->columnSpanFull(),
            ]);
    }
}
