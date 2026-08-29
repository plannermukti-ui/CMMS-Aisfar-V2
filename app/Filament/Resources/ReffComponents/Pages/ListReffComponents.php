<?php

namespace App\Filament\Resources\ReffComponents\Pages;

use App\Filament\Resources\ReffComponents\ReffComponentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListReffComponents extends ListRecords
{
    protected static string $resource = ReffComponentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
