<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\ReportBudgetVsActual;
use App\Filament\Widgets\ReportCategoryBreakdown;
use App\Filament\Widgets\ReportMonthOverMonth;
use App\Filament\Widgets\ReportOverviewStats;
use App\Filament\Widgets\ReportPersonBreakdown;
use App\Filament\Widgets\ReportSpendingTrend;
use App\Filament\Widgets\ReportTopExpenses;
use App\Support\Money;
use App\Support\ReportData;
use App\Support\ReportPeriod;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Schemas\Schema;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Per-account Reports dashboard (/admin/reports and /app/reports). A single
 * "period" filter drives every account-scoped report widget. Lives only in the
 * per-account panels — the SuperAdmin panel has its own platform reports.
 */
class Reports extends BaseDashboard
{
    use HasFiltersForm;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-pie';

    protected static ?string $navigationLabel = 'Reports';

    protected static ?string $title = 'Reports';

    protected static ?int $navigationSort = 1;

    protected static string $routePath = 'reports';

    public function filtersForm(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('period')
                ->label('Period')
                ->options(ReportPeriod::options())
                ->default(ReportPeriod::DEFAULT)
                ->selectablePlaceholder(false)
                ->native(false),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('print')
                ->label('Printable / PDF')
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->openUrlInNewTab()
                ->url(fn (): string => route('reports.print', [
                    'period' => $this->filters['period'] ?? ReportPeriod::DEFAULT,
                ])),

            Action::make('exportCsv')
                ->label('Export CSV')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(fn (): StreamedResponse => $this->exportCsv()),
        ];
    }

    public function getWidgets(): array
    {
        return [
            ReportOverviewStats::class,
            ReportMonthOverMonth::class,
            ReportSpendingTrend::class,
            ReportCategoryBreakdown::class,
            ReportPersonBreakdown::class,
            ReportBudgetVsActual::class,
            ReportTopExpenses::class,
        ];
    }

    /** Multi-section CSV of the current period (opens in Excel/Sheets). */
    protected function exportCsv(): StreamedResponse
    {
        $period = ReportPeriod::resolve($this->filters['period'] ?? null);
        [$start, $end] = [$period['start'], $period['end']];
        $ccy = Money::current();

        $totals = ReportData::totals($start, $end);
        $byCategory = ReportData::byCategory($start, $end);
        $byList = ReportData::byList($start, $end);
        $top = ReportData::topExpenses($start, $end, 50);

        $filename = 'kharcha-report-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($period, $ccy, $totals, $byCategory, $byList, $top, $start, $end) {
            $out = fopen('php://output', 'w');

            fputcsv($out, ['Kharcha report', $period['label'], $start->format('Y-m-d').' to '.$end->format('Y-m-d')]);
            fputcsv($out, ['Currency', $ccy]);
            fputcsv($out, ['Total spent', $totals['total'], 'Entries', $totals['count'], 'Average', $totals['average']]);
            fputcsv($out, []);

            fputcsv($out, ['By category', 'Entries', 'Total']);
            foreach ($byCategory as $r) {
                fputcsv($out, [$r['name'], $r['count'], $r['total']]);
            }
            fputcsv($out, []);

            fputcsv($out, ['By list', 'Type', 'Budget/mo', 'Spent']);
            foreach ($byList as $r) {
                fputcsv($out, [$r['name'], $r['type'], $r['budget'], $r['total']]);
            }
            fputcsv($out, []);

            fputcsv($out, ['Largest expenses', 'Date', 'List', 'Category', 'Amount', 'Original']);
            foreach ($top as $e) {
                fputcsv($out, [
                    $e->item_name,
                    $e->purchased_at?->format('Y-m-d'),
                    $e->spendingList?->name,
                    $e->category?->name,
                    $e->amount,
                    $e->original_amount !== null ? $e->original_amount.' '.$e->original_currency : '',
                ]);
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function getColumns(): int|array
    {
        return 2;
    }
}
