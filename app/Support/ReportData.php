<?php

namespace App\Support;

use App\Models\Category;
use App\Models\Entry;
use App\Models\SpendingList;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

/**
 * Account-scoped report aggregations for a [start, end] window. Shared by the
 * CSV export and the printable report so both read identically. Queries run
 * through AccountScope, so they only see the current tenant's data.
 */
class ReportData
{
    /** @return array{total:float,count:int,average:float} */
    public static function totals(CarbonInterface $start, CarbonInterface $end): array
    {
        $q = Entry::query()->whereBetween('purchased_at', [$start, $end]);
        $total = (float) $q->clone()->sum('amount');
        $count = (int) $q->clone()->count();

        return [
            'total' => round($total, 2),
            'count' => $count,
            'average' => $count > 0 ? round($total / $count, 2) : 0.0,
        ];
    }

    /** @return Collection<int,array{name:string,total:float,count:int}> */
    public static function byCategory(CarbonInterface $start, CarbonInterface $end): Collection
    {
        $names = Category::pluck('name', 'id');

        return Entry::query()
            ->whereBetween('purchased_at', [$start, $end])
            ->selectRaw('category_id, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('category_id')
            ->get()
            ->map(fn ($r) => [
                'name' => $r->category_id ? ($names[$r->category_id] ?? 'Unknown') : 'Uncategorised',
                'total' => (float) $r->total,
                'count' => (int) $r->count,
            ])
            ->sortByDesc('total')
            ->values();
    }

    /** @return Collection<int,array{name:string,type:string,total:float,budget:?float}> */
    public static function byList(CarbonInterface $start, CarbonInterface $end): Collection
    {
        return SpendingList::query()
            ->withSum(['entries as total' => fn ($q) => $q->whereBetween('purchased_at', [$start, $end])], 'amount')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (SpendingList $l) => [
                'name' => $l->name,
                'type' => $l->type,
                'total' => (float) ($l->total ?? 0),
                'budget' => $l->monthly_budget !== null ? (float) $l->monthly_budget : null,
            ]);
    }

    /** @return Collection<int,Entry> */
    public static function topExpenses(CarbonInterface $start, CarbonInterface $end, int $limit = 20): Collection
    {
        return Entry::query()
            ->with(['spendingList', 'category'])
            ->whereBetween('purchased_at', [$start, $end])
            ->orderByDesc('amount')
            ->limit($limit)
            ->get();
    }
}
