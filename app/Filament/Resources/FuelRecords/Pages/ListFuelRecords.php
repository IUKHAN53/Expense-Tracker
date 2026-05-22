<?php

namespace App\Filament\Resources\FuelRecords\Pages;

use App\Filament\Resources\FuelRecords\FuelRecordResource;
use App\Filament\Widgets\FuelTrend;
use App\Services\FuelCsvImporter;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ListFuelRecords extends ListRecords
{
    protected static string $resource = FuelRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('importCsv')
                ->label('Import CSV')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('gray')
                ->modalHeading('Import CarExpenses CSV')
                ->modalDescription('Upload a TSV/CSV export from the CarExpenses Android app. Existing refuels are skipped.')
                ->schema([
                    FileUpload::make('file')
                        ->label('CarExpenses export')
                        ->disk('local')
                        ->directory('imports')
                        ->required()
                        ->preserveFilenames(),
                ])
                ->action(function (array $data): void {
                    $path = Storage::disk('local')->path($data['file']);
                    try {
                        $result = (new FuelCsvImporter())->importFile($path);
                        Notification::make()
                            ->title('Import complete')
                            ->body("Imported {$result['imported']} · skipped {$result['skipped']}".($result['errors'] ? " · errors {$result['errors']}" : ''))
                            ->success()
                            ->send();
                    } catch (Throwable $e) {
                        Notification::make()
                            ->title('Import failed')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    } finally {
                        Storage::disk('local')->delete($data['file']);
                    }
                }),
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            FuelTrend::class,
        ];
    }
}
