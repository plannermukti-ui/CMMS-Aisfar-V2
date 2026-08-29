<?php

namespace App\Filament\Resources\Part\Pages;

use App\Filament\Resources\Part\PartResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListParts extends ListRecords
{
    protected static string $resource = PartResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
