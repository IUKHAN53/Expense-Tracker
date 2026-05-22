<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Entry;
use App\Models\Receipt;
use App\Models\SpendingList;
use App\Services\GeminiService;
use App\Support\Fuel;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ReceiptController extends Controller
{
    /**
     * Upload a receipt/bill photo, run it through Gemini, and return the
     * extracted line items ready for the user to assign to people/lists.
     * Petrol receipts are auto-routed to the Car (vehicle) list.
     */
    public function scan(Request $request, GeminiService $gemini)
    {
        $request->validate([
            'image' => ['required', 'image', 'max:12288'], // 12 MB
        ]);

        $file = $request->file('image');
        $path = $file->store('receipts', 'public');

        $receipt = Receipt::create([
            'image_path' => $path,
            'status' => 'pending',
        ]);

        try {
            $parsed = $gemini->parseReceipt(
                Storage::disk('public')->path($path),
                $file->getMimeType(),
            );
        } catch (Throwable $e) {
            $receipt->update(['status' => 'failed', 'error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Could not read the receipt. '.$e->getMessage(),
                'receipt' => $this->present($receipt),
            ], 422);
        }

        $purchasedAt = $this->safeDate($parsed['purchased_at']);

        $receipt->update([
            'merchant' => $parsed['merchant'],
            'receipt_type' => $parsed['receipt_type'],
            'total' => $parsed['total'],
            'purchased_at' => $purchasedAt,
            'raw_json' => $parsed['raw'],
            'status' => 'parsed',
        ]);

        $isFuel = $parsed['receipt_type'] === Receipt::TYPE_FUEL
            || Fuel::looksLikeFuel($parsed['merchant']);

        $carList = $isFuel ? SpendingList::where('type', SpendingList::TYPE_VEHICLE)->first() : null;
        $homeList = SpendingList::where('type', SpendingList::TYPE_HOUSEHOLD)->first();
        $defaultListId = $isFuel ? $carList?->id : $homeList?->id;

        $categories = Category::all();

        $items = collect($parsed['items'])->map(function (array $item) use ($categories, $defaultListId) {
            $category = $categories->first(
                fn (Category $c) => strcasecmp($c->name, (string) $item['suggested_category']) === 0
            );

            return [
                'item_name' => $item['name'],
                'amount' => $item['amount'],
                'quantity' => $item['quantity'],
                'unit' => $item['unit'],
                'unit_price' => $item['unit_price'],
                'category_id' => $category?->id,
                'suggested_list_id' => $defaultListId,
            ];
        })->values();

        return response()->json([
            'receipt' => $this->present($receipt),
            'is_fuel' => $isFuel,
            'auto_assigned_list_id' => $isFuel ? $carList?->id : null,
            'default_list_id' => $defaultListId,
            'fuel' => [
                'liters' => $parsed['fuel_liters'],
                'rate' => $parsed['fuel_rate'],
            ],
            'items' => $items,
        ]);
    }

    /**
     * Persist the (possibly edited) line items as entries, each assigned
     * to a spending list chosen by the user.
     */
    public function confirm(Request $request, Receipt $receipt)
    {
        $data = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.spending_list_id' => ['required', 'integer', 'exists:spending_lists,id'],
            'items.*.category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'items.*.item_name' => ['required', 'string', 'max:255'],
            'items.*.amount' => ['required', 'numeric', 'min:0'],
            'items.*.quantity' => ['nullable', 'numeric', 'min:0'],
            'items.*.unit' => ['nullable', 'string', 'max:50'],
            'items.*.fuel_liters' => ['nullable', 'numeric', 'min:0'],
            'items.*.fuel_rate' => ['nullable', 'numeric', 'min:0'],
            'items.*.odometer' => ['nullable', 'integer', 'min:0'],
        ]);

        $count = DB::transaction(function () use ($data, $receipt) {
            foreach ($data['items'] as $item) {
                Entry::create([
                    'spending_list_id' => $item['spending_list_id'],
                    'category_id' => $item['category_id'] ?? null,
                    'receipt_id' => $receipt->id,
                    'item_name' => $item['item_name'],
                    'amount' => $item['amount'],
                    'quantity' => $item['quantity'] ?? 1,
                    'unit' => $item['unit'] ?? null,
                    'purchased_at' => $receipt->purchased_at ?? now(),
                    'source' => Entry::SOURCE_SCAN,
                    'fuel_liters' => $item['fuel_liters'] ?? null,
                    'fuel_rate' => $item['fuel_rate'] ?? null,
                    'odometer' => $item['odometer'] ?? null,
                ]);
            }

            $receipt->update(['status' => 'assigned']);

            return count($data['items']);
        });

        return response()->json([
            'message' => $count.' '.str('entry')->plural($count).' saved.',
            'count' => $count,
        ], 201);
    }

    public function index()
    {
        $receipts = Receipt::query()
            ->latest()
            ->limit(100)
            ->get()
            ->map(fn (Receipt $r) => $this->present($r));

        return response()->json(['data' => $receipts]);
    }

    public function show(Receipt $receipt)
    {
        return response()->json([
            'data' => $this->present($receipt->load('entries.spendingList')),
        ]);
    }

    private function safeDate(?string $value): ?Carbon
    {
        if (! $value) {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (Throwable) {
            return null;
        }
    }

    private function present(Receipt $receipt): array
    {
        return [
            'id' => $receipt->id,
            'merchant' => $receipt->merchant,
            'receipt_type' => $receipt->receipt_type,
            'total' => $receipt->total !== null ? (float) $receipt->total : null,
            'purchased_at' => $receipt->purchased_at?->toIso8601String(),
            'status' => $receipt->status,
            'error' => $receipt->error,
            'image_url' => $receipt->image_path ? Storage::disk('public')->url($receipt->image_path) : null,
            'created_at' => $receipt->created_at?->toIso8601String(),
        ];
    }
}
