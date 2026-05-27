<?php

namespace App\Filament\Resources\Receipts\Tables;

use App\Models\Account;
use App\Models\Receipt;
use App\Models\User;
use App\Support\MonthTableFilter;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ReceiptsTable
{
    public static function configure(Table $table): Table
    {
        $isSuperAdmin = (bool) Auth::user()?->isSuperAdmin();

        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                ImageColumn::make('image_path')
                    ->label('Photo')
                    ->disk('public')
                    ->size(56)
                    ->square()
                    ->action(self::viewAction()),
                TextColumn::make('merchant')
                    ->searchable()
                    ->placeholder('·'),
                TextColumn::make('receipt_type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'fuel' => 'danger',
                        'grocery' => 'success',
                        'pharmacy' => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('total')
                    ->money('PKR')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'assigned' => 'success',
                        'parsed' => 'info',
                        'failed' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('createdBy.name')
                    ->label('Added by')
                    ->placeholder('·')
                    ->toggleable(),
                TextColumn::make('account.name')
                    ->label('Household')
                    ->visible($isSuperAdmin)
                    ->toggleable(),
                TextColumn::make('purchased_at')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->placeholder('·'),
                TextColumn::make('created_at')
                    ->label('Scanned')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->filters([
                MonthTableFilter::make('created_at'),
                SelectFilter::make('receipt_type')
                    ->options([
                        'grocery' => 'Grocery',
                        'fuel' => 'Fuel',
                        'pharmacy' => 'Pharmacy',
                        'other' => 'Other',
                    ]),
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'parsed' => 'Parsed',
                        'assigned' => 'Assigned',
                        'failed' => 'Failed',
                    ]),
                SelectFilter::make('created_by_user_id')
                    ->label('Added by')
                    ->options(fn () => self::userOptions($isSuperAdmin))
                    ->searchable(),
                SelectFilter::make('account_id')
                    ->label('Household')
                    ->options(fn () => Account::orderBy('name')->pluck('name', 'id')->all())
                    ->searchable()
                    ->visible($isSuperAdmin),
            ])
            ->recordActions([
                self::viewAction(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     * Opens the receipt photo in a modal at near-viewport size plus the
     * parsed metadata. Clicking the image inside the modal opens the raw
     * file in a new tab.
     */
    private static function viewAction(): Action
    {
        return Action::make('view-receipt')
            ->label('View')
            ->icon('heroicon-o-magnifying-glass-plus')
            ->modalHeading(fn (Receipt $record) => 'Receipt · '.($record->merchant ?? '#'.$record->id))
            ->modalContent(fn (Receipt $record) => view('filament.receipt-modal', ['receipt' => $record]))
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Close')
            ->modalWidth('5xl');
    }

    private static function userOptions(bool $isSuperAdmin): array
    {
        $query = User::query()->orderBy('name');
        if (! $isSuperAdmin) {
            $query->where('account_id', Auth::user()?->account_id);
        }

        return $query->get(['id', 'name', 'email'])
            ->mapWithKeys(fn (User $u) => [$u->id => $u->name.' ('.$u->email.')'])
            ->all();
    }
}
