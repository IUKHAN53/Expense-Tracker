<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SpendingList;
use App\Support\MonthRange;
use Illuminate\Http\Request;

class SpendingListController extends Controller
{
    /** All spending lists with their total for the requested month. */
    public function index(Request $request)
    {
        [$from, $to] = MonthRange::resolve($request->query('month'));

        $lists = SpendingList::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->withSum(['entries as month_total' => function ($q) use ($from, $to) {
                $q->whereBetween('purchased_at', [$from, $to]);
            }], 'amount')
            ->withCount(['entries as month_entries_count' => function ($q) use ($from, $to) {
                $q->whereBetween('purchased_at', [$from, $to]);
            }])
            ->get()
            ->map(fn (SpendingList $list) => $this->present($list));

        return response()->json([
            'data' => $lists,
            'month' => $from->format('Y-m'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'type' => ['required', 'in:person,household,vehicle'],
            'color' => ['nullable', 'string', 'max:20'],
            'icon' => ['nullable', 'string', 'max:100'],
            'monthly_budget' => ['nullable', 'numeric', 'min:0'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $list = SpendingList::create($data);

        return response()->json(['data' => $this->present($list)], 201);
    }

    public function update(Request $request, SpendingList $list)
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:100'],
            'type' => ['sometimes', 'in:person,household,vehicle'],
            'color' => ['nullable', 'string', 'max:20'],
            'icon' => ['nullable', 'string', 'max:100'],
            'monthly_budget' => ['nullable', 'numeric', 'min:0'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $list->update($data);

        return response()->json(['data' => $this->present($list)]);
    }

    public function destroy(SpendingList $list)
    {
        $list->delete();

        return response()->json(['message' => 'List deleted.']);
    }

    private function present(SpendingList $list): array
    {
        $monthTotal = (float) ($list->month_total ?? 0);
        $budget = $list->monthly_budget !== null ? (float) $list->monthly_budget : null;

        return [
            'id' => $list->id,
            'name' => $list->name,
            'type' => $list->type,
            'color' => $list->color,
            'icon' => $list->icon,
            'monthly_budget' => $budget,
            'sort_order' => $list->sort_order,
            'is_active' => $list->is_active,
            'month_total' => $monthTotal,
            'month_entries_count' => (int) ($list->month_entries_count ?? 0),
            'budget_remaining' => $budget !== null ? round($budget - $monthTotal, 2) : null,
        ];
    }
}
