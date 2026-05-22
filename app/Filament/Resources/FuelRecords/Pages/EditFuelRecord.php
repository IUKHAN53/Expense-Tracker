<?php

namespace App\Filament\Resources\FuelRecords\Pages;

use App\Filament\Resources\FuelRecords\FuelRecordResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFuelRecord extends EditRecord
{
    protected static string $resource = FuelRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
