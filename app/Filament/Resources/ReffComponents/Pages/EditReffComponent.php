<?php

namespace App\Filament\Resources\ReffComponents\Pages;

use App\Filament\Resources\ReffComponents\ReffComponentResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditReffComponent extends EditRecord
{
    protected static string $resource = ReffComponentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
