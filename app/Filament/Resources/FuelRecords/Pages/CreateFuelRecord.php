<?php

namespace App\Filament\Resources\FuelRecords\Pages;

use App\Filament\Resources\FuelRecords\FuelRecordResource;
use App\Models\Entry;
use Filament\Resources\Pages\CreateRecord;

class CreateFuelRecord extends CreateRecord
{
    protected static string $resource = FuelRecordResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['source'] = Entry::SOURCE_MANUAL;
        $data['quantity'] = 1;
        $data['unit'] = 'ltr';
        $data['item_name'] = $data['item_name'] ?? ('Fuel · '.($data['fuel_type'] ?? 'E92'));

        return $data;
    }
}
