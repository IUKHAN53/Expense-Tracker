<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Entry;
use App\Models\SpendingList;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;

/**
 * Fuel records + summary for the Car list — used by the app's Fuel tab
 * and by the dashboard charts.
 *
 * "km per litre" is computed per fill as
 *     (odometer of this fill - odometer of previous fill) / litres of this fill.
 * Fills without an odometer are still shown but skipped when chaining.
 */
class FuelController extends Controller
{
    public function index(Request $request)
    {
        $carList = SpendingList::where('type', SpendingList::TYPE_VEHICLE)->first();

        if (! $carList) {
            return response()->json(['records' => [], 'stats' => $this->emptyStats()]);
        }

        $entries = Entry::query()
            ->where('spending_list_id', $carList->id)
            ->whereNotNull('fuel_liters')
            ->where('fuel_liters', '>', 0)
            ->orderBy('purchased_at')
            ->orderBy('id')
            ->get();

        // Compute km_since_last + km_per_liter walking forward.
        $records = [];
        $prevOdometer = null;
        foreach ($entries as $e) {
            $kmSince = null;
            $kmpl = null;
            $odo = $e->odometer;
            $liters = (float) $e->fuel_liters;

            if ($odo !== null && $prevOdometer !== null && $odo > $prevOdometer && $liters > 0) {
                $kmSince = $odo - $prevOdometer;
                $kmpl = round($kmSince / $liters, 2);
            }

            $rsPerKm = ($kmSince !== null && $kmSince > 0)
                ? round((float) $e->amount / $kmSince, 2)
                : null;

            $records[] = [
                'id' => $e->id,
                'date' => $e->purchased_at?->toIso8601String(),
                'item_name' => $e->item_name,
                'odometer' => $odo,
                'liters' => round($liters, 2),
                'rate' => $e->fuel_rate !== null ? (float) $e->fuel_rate : null,
                'amount' => (float) $e->amount,
                'fuel_type' => $e->fuel_type ?: 'E92',
                'is_full_tank' => (bool) $e->is_full_tank,
                'notes' => $e->notes,
                'km_since_last' => $kmSince,
                'km_per_liter' => $kmpl,
                'rs_per_km' => $rsPerKm,
            ];

            if ($odo !== null) {
                $prevOdometer = $odo;
            }
        }

        // Most recent first for display.
        $records = array_reverse($records);

        return response()->json([
            'records' => $records,
            'stats' => $this->buildStats($entries, $records),
        ]);
    }

    private function buildStats($entries, array $records): array
    {
        if ($entries->isEmpty()) {
            return $this->emptyStats();
        }

        $last = $entries->last();

        // Avg km/l from the last 5 valid per-fill calculations.
        $kmpls = collect($records)->pluck('km_per_liter')->filter(fn ($v) => $v > 0)->values();
        $avg = $kmpls->isNotEmpty()
            ? round($kmpls->take(5)->avg(), 2)
            : null;
        $avgAllTime = $kmpls->isNotEmpty()
            ? round($kmpls->avg(), 2)
            : null;

        $monthStart = CarbonImmutable::now()->startOfMonth();
        $monthEntries = $entries->filter(fn ($e) => $e->purchased_at && $e->purchased_at->greaterThanOrEqualTo($monthStart));

        $monthOdos = $monthEntries->pluck('odometer')->filter()->values();
        $monthKms = $monthOdos->count() >= 2
            ? (int) ($monthOdos->max() - $monthOdos->min())
            : 0;

        return [
            'avg_km_per_liter' => $avg,
            'avg_km_per_liter_all_time' => $avgAllTime,
            'avg_window' => 'last 5 fills',
            'last_fill_date' => $last->purchased_at?->toIso8601String(),
            'last_odometer' => $last->odometer,
            'last_fuel_type' => $last->fuel_type,
            'month_liters' => round($monthEntries->sum(fn ($e) => (float) $e->fuel_liters), 2),
            'month_spent' => round($monthEntries->sum(fn ($e) => (float) $e->amount), 2),
            'month_kms' => $monthKms,
            'total_records' => $entries->count(),
            'all_time_liters' => round($entries->sum(fn ($e) => (float) $e->fuel_liters), 2),
            'all_time_spent' => round($entries->sum(fn ($e) => (float) $e->amount), 2),
        ];
    }

    private function emptyStats(): array
    {
        return [
            'avg_km_per_liter' => null,
            'avg_km_per_liter_all_time' => null,
            'avg_window' => 'last 5 fills',
            'last_fill_date' => null,
            'last_odometer' => null,
            'last_fuel_type' => null,
            'month_liters' => 0,
            'month_spent' => 0,
            'month_kms' => 0,
            'total_records' => 0,
            'all_time_liters' => 0,
            'all_time_spent' => 0,
        ];
    }
}
