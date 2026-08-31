<?php

namespace App\Filament\Resources\PmUnitModels\Pages;

use App\Filament\Resources\PmUnitModels\PmUnitModelResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPmUnitModels extends ListRecords
{
    protected static string $resource = PmUnitModelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
