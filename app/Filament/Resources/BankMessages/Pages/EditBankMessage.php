<?php

namespace App\Filament\Resources\BankMessages\Pages;

use App\Filament\Resources\BankMessages\BankMessageResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBankMessage extends EditRecord
{
    protected static string $resource = BankMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
