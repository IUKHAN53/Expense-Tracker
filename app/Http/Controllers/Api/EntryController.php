<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Entry;
use App\Support\MonthRange;
use Illuminate\Http\Request;

class EntryController extends Controller
{
    /** List entries, newest first, with optional filters. */
    public function index(Request $request)
    {
        $data = $request->validate([
            'spending_list_id' => ['nullable', 'integer', 'exists:spending_lists,id'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'month' => ['nullable', 'regex:/^\d{4}-\d{2}$/'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
            'source' => ['nullable', 'in:manual,scan,sms'],
            'search' => ['nullable', 'string', 'max:100'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:1000'],
        ]);

        $query = Entry::query()
            ->with(['spendingList', 'category'])
            ->orderByDesc('purchased_at')
            ->orderByDesc('id');

        if (! empty($data['spending_list_id'])) {
            $query->where('spending_list_id', $data['spending_list_id']);
        }

        if (! empty($data['category_id'])) {
            $query->where('category_id', $data['category_id']);
        }

        if (! empty($data['source'])) {
            $query->where('source', $data['source']);
        }

        if (! empty($data['search'])) {
            $query->where('item_name', 'like', '%'.$data['search'].'%');
        }

        if (! empty($data['month'])) {
            [$from, $to] = MonthRange::resolve($data['month']);
            $query->whereBetween('purchased_at', [$from, $to]);
        } else {
            if (! empty($data['from'])) {
                $query->where('purchased_at', '>=', $data['from']);
            }
            if (! empty($data['to'])) {
                $query->where('purchased_at', '<=', $data['to']);
            }
        }

        $entries = $query->limit($data['limit'] ?? 500)->get()
            ->map(fn (Entry $entry) => $this->present($entry));

        return response()->json([
            'data' => $entries,
            'total' => round($entries->sum('amount'), 2),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            // Either a single list id (regular entry) or an array of list ids
            // (split — the amount is divided equally between them).
            'spending_list_id' => ['required_without:spending_list_ids', 'integer', 'exists:spending_lists,id'],
            'spending_list_ids' => ['required_without:spending_list_id', 'array', 'min:1', 'max:10'],
            'spending_list_ids.*' => ['integer', 'distinct', 'exists:spending_lists,id'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'item_name' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'quantity' => ['nullable', 'numeric', 'min:0'],
            'unit' => ['nullable', 'string', 'max:50'],
            'purchased_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'fuel_liters' => ['nullable', 'numeric', 'min:0'],
            'fuel_rate' => ['nullable', 'numeric', 'min:0'],
            'odometer' => ['nullable', 'integer', 'min:0'],
            'fuel_type' => ['nullable', 'string', 'max:16'],
            'is_full_tank' => ['nullable', 'boolean'],
        ]);

        $listIds = $data['spending_list_ids'] ?? [$data['spending_list_id']];
        unset($data['spending_list_id'], $data['spending_list_ids']);

        $data['quantity'] = $data['quantity'] ?? 1;
        $data['purchased_at'] = $data['purchased_at'] ?? now();
        $data['source'] = Entry::SOURCE_MANUAL;

        // Split mode — divide the amount equally and link the shares with a UUID.
        if (count($listIds) > 1) {
            $share = round((float) $data['amount'] / count($listIds), 2);
            $groupId = (string) \Illuminate\Support\Str::uuid();

            $entries = collect($listIds)->map(function (int $listId) use ($data, $share, $groupId) {
                return Entry::create(array_merge($data, [
                    'spending_list_id' => $listId,
                    'amount' => $share,
                    'split_group_id' => $groupId,
                ]));
            })->all();

            return response()->json([
                'data' => array_map(
                    fn ($e) => $this->present($e->load(['spendingList', 'category'])),
                    $entries,
                ),
                'split_group_id' => $groupId,
                'split_share' => $share,
            ], 201);
        }

        $entry = Entry::create(array_merge($data, [
            'spending_list_id' => $listIds[0],
        ]));

        return response()->json([
            'data' => $this->present($entry->load(['spendingList', 'category'])),
        ], 201);
    }

    public function show(Entry $entry)
    {
        return response()->json([
            'data' => $this->present($entry->load(['spendingList', 'category'])),
        ]);
    }

    public function update(Request $request, Entry $entry)
    {
        $data = $request->validate([
            'spending_list_id' => ['sometimes', 'integer', 'exists:spending_lists,id'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'item_name' => ['sometimes', 'string', 'max:255'],
            'amount' => ['sometimes', 'numeric', 'min:0'],
            'quantity' => ['nullable', 'numeric', 'min:0'],
            'unit' => ['nullable', 'string', 'max:50'],
            'purchased_at' => ['sometimes', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'fuel_liters' => ['nullable', 'numeric', 'min:0'],
            'fuel_rate' => ['nullable', 'numeric', 'min:0'],
            'odometer' => ['nullable', 'integer', 'min:0'],
            'fuel_type' => ['nullable', 'string', 'max:16'],
            'is_full_tank' => ['nullable', 'boolean'],
        ]);

        $entry->update($data);

        return response()->json([
            'data' => $this->present($entry->fresh()->load(['spendingList', 'category'])),
        ]);
    }

    public function destroy(Entry $entry)
    {
        $entry->delete();

        return response()->json(['message' => 'Entry deleted.']);
    }

    private function present(Entry $entry): array
    {
        return [
            'id' => $entry->id,
            'spending_list_id' => $entry->spending_list_id,
            'spending_list' => $entry->spendingList ? [
                'id' => $entry->spendingList->id,
                'name' => $entry->spendingList->name,
                'type' => $entry->spendingList->type,
                'color' => $entry->spendingList->color,
            ] : null,
            'category_id' => $entry->category_id,
            'category' => $entry->category ? [
                'id' => $entry->category->id,
                'name' => $entry->category->name,
                'color' => $entry->category->color,
            ] : null,
            'receipt_id' => $entry->receipt_id,
            'bank_message_id' => $entry->bank_message_id,
            'item_name' => $entry->item_name,
            'amount' => (float) $entry->amount,
            'quantity' => (float) $entry->quantity,
            'unit' => $entry->unit,
            'purchased_at' => $entry->purchased_at?->toIso8601String(),
            'source' => $entry->source,
            'notes' => $entry->notes,
            'fuel_liters' => $entry->fuel_liters !== null ? (float) $entry->fuel_liters : null,
            'fuel_rate' => $entry->fuel_rate !== null ? (float) $entry->fuel_rate : null,
            'odometer' => $entry->odometer,
            'fuel_type' => $entry->fuel_type,
            'is_full_tank' => $entry->is_full_tank,
            'possible_duplicate_of_entry_id' => $entry->possible_duplicate_of_entry_id,
            'split_group_id' => $entry->split_group_id,
        ];
    }
}
