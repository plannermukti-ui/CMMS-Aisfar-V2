<?php

namespace App\Filament\Resources\ReffUsers\Pages;

use App\Filament\Resources\ReffUsers\ReffUserResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageReffUsers extends ManageRecords
{
    protected static string $resource = ReffUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
