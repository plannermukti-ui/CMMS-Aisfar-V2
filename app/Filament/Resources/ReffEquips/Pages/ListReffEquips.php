<?php

namespace App\Filament\Resources\ReffEquips\Pages;

use App\Filament\Resources\ReffEquips\ReffEquipResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListReffEquips extends ListRecords
{
    protected static string $resource = ReffEquipResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
