<?php

namespace App\Filament\Resources\SpendingLists\Pages;

use App\Filament\Resources\SpendingLists\SpendingListResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSpendingList extends EditRecord
{
    protected static string $resource = SpendingListResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
