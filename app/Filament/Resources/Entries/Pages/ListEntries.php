<?php

namespace App\Filament\Resources\Entries\Pages;

use App\Filament\Resources\Entries\EntryResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ListEntries extends ListRecords
{
    protected static string $resource = EntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->exportAction(),
            CreateAction::make(),
        ];
    }

    /**
     * Stream the visible entries as a CSV. Respects AccountScope (a tenant
     * exports only their own data; a SuperAdmin exports across tenants) and
     * any filters/search active on the table.
     */
    protected function exportAction(): Action
    {
        return Action::make('export')
            ->label('Export CSV')
            ->icon('heroicon-o-arrow-down-tray')
            ->color('gray')
            ->action(function (): StreamedResponse {
                $query = $this->getFilteredTableQuery()
                    ->with(['spendingList', 'category', 'account'])
                    ->reorder()
                    ->orderByDesc('purchased_at');

                $filename = 'kharcha-entries-'.now()->format('Y-m-d').'.csv';

                return response()->streamDownload(function () use ($query) {
                    $out = fopen('php://output', 'w');
                    fputcsv($out, [
                        'Date', 'Item', 'List', 'Category', 'Amount',
                        'Original amount', 'Original currency', 'Source', 'Household',
                    ]);

                    $query->chunk(500, function ($rows) use ($out) {
                        foreach ($rows as $e) {
                            fputcsv($out, [
                                $e->purchased_at?->format('Y-m-d H:i'),
                                $e->item_name,
                                $e->spendingList?->name,
                                $e->category?->name,
                                $e->amount,
                                $e->original_amount,
                                $e->original_currency,
                                $e->source,
                                $e->account?->name,
                            ]);
                        }
                    });

                    fclose($out);
                }, $filename, ['Content-Type' => 'text/csv']);
            });
    }
}
