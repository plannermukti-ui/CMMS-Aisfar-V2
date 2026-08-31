<?php

namespace App\Filament\Resources\PmUnitModels\Pages;

use App\Filament\Resources\PmUnitModels\PmUnitModelResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditPmUnitModel extends EditRecord
{
    protected static string $resource = PmUnitModelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
