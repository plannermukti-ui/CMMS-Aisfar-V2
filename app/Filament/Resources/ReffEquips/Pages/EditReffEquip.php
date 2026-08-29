<?php

namespace App\Filament\Resources\ReffEquips\Pages;

use App\Filament\Resources\ReffEquips\ReffEquipResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditReffEquip extends EditRecord
{
    protected static string $resource = ReffEquipResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
