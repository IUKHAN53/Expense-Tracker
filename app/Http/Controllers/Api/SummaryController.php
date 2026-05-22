<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Entry;
use App\Models\SpendingList;
use App\Support\MonthRange;
use Illuminate\Http\Request;

class SummaryController extends Controller
{
    /** Monthly spending overview: per-list totals, grand total, category split. */
    public function index(Request $request)
    {
        $request->validate([
            'month' => ['nullable', 'regex:/^\d{4}-\d{2}$/'],
        ]);

        [$from, $to] = MonthRange::resolve($request->query('month'));

        $lists = SpendingList::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->withSum(['entries as total' => function ($q) use ($from, $to) {
                $q->whereBetween('purchased_at', [$from, $to]);
            }], 'amount')
            ->get()
            ->map(function (SpendingList $list) {
                $total = (float) ($list->total ?? 0);
                $budget = $list->monthly_budget !== null ? (float) $list->monthly_budget : null;

                return [
                    'id' => $list->id,
                    'name' => $list->name,
                    'type' => $list->type,
                    'color' => $list->color,
                    'monthly_budget' => $budget,
                    'total' => $total,
                    'budget_remaining' => $budget !== null ? round($budget - $total, 2) : null,
                ];
            });

        $categories = Category::pluck('name', 'id');

        $byCategory = Entry::query()
            ->whereBetween('purchased_at', [$from, $to])
            ->selectRaw('category_id, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('category_id')
            ->get()
            ->map(fn ($row) => [
                'category_id' => $row->category_id,
                'category_name' => $row->category_id ? ($categories[$row->category_id] ?? 'Unknown') : 'Uncategorised',
                'total' => (float) $row->total,
                'count' => (int) $row->count,
            ])
            ->sortByDesc('total')
            ->values();

        return response()->json([
            'month' => $from->format('Y-m'),
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'grand_total' => round($lists->sum('total'), 2),
            'lists' => $lists,
            'by_category' => $byCategory,
        ]);
    }
}
