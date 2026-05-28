<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Entries\EntryResource;
use App\Filament\Resources\Receipts\ReceiptResource;
use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            UserResource::impersonateAction(),

            Action::make('viewEntries')
                ->label('View entries')
                ->icon('heroicon-o-banknotes')
                ->color('gray')
                ->url(fn (User $record) => EntryResource::getUrl('index', [
                    'tableFilters' => ['created_by_user_id' => ['value' => $record->id]],
                ])),

            Action::make('viewReceipts')
                ->label('View receipts')
                ->icon('heroicon-o-camera')
                ->color('gray')
                ->url(fn (User $record) => ReceiptResource::getUrl('index', [
                    'tableFilters' => ['created_by_user_id' => ['value' => $record->id]],
                ])),

            EditAction::make(),
        ];
    }
}
