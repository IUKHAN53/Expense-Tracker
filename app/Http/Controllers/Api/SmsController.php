<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BankMessage;
use App\Models\Category;
use App\Models\Entry;
use App\Models\SpendingList;
use App\Services\GeminiService;
use App\Support\Fuel;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Throwable;

class SmsController extends Controller
{
    /**
     * Import transaction SMS messages read from the phone.
     *
     * Each message is de-duplicated by hash, parsed by Gemini, and - when
     * it is a real spend - turned into an Entry. Fuel-station transactions
     * are routed to the Car list; everything else lands on Home, ready to
     * be re-assigned by the user with one tap.
     */
    public function import(Request $request, GeminiService $gemini)
    {
        $data = $request->validate([
            'messages' => ['required', 'array', 'min:1', 'max:80'],
            'messages.*.body' => ['required', 'string', 'max:2000'],
            'messages.*.sender' => ['nullable', 'string', 'max:100'],
            'messages.*.received_at' => ['nullable'],
        ]);

        $homeList = SpendingList::where('type', SpendingList::TYPE_HOUSEHOLD)->first();
        $carList = SpendingList::where('type', SpendingList::TYPE_VEHICLE)->first();
        $fallbackList = $homeList ?? SpendingList::orderBy('sort_order')->orderBy('id')->first();

        if (! $fallbackList) {
            return response()->json([
                'message' => 'No spending lists exist yet. Create at least one list first.',
            ], 422);
        }

        $fuelCategoryId = Category::where('name', 'Fuel')->value('id');

        $summary = [
            'received' => count($data['messages']),
            'imported' => 0,
            'skipped' => 0,
            'transactions' => 0,
            'ignored' => 0,
            'failed' => 0,
            'messages' => [],
        ];

        foreach ($data['messages'] as $msg) {
            $body = trim($msg['body']);
            $sender = $msg['sender'] ?? null;
            $receivedAt = $this->parseReceivedAt($msg['received_at'] ?? null);
            $hash = hash('sha256', ($sender ?? '').'|'.$body.'|'.($receivedAt?->toDateTimeString() ?? ''));

            if (BankMessage::where('sms_hash', $hash)->exists()) {
                $summary['skipped']++;

                continue;
            }

            $record = new BankMessage([
                'sender' => $sender,
                'body' => $body,
                'sms_hash' => $hash,
                'received_at' => $receivedAt,
                'status' => 'pending',
            ]);

            try {
                $parsed = $gemini->parseSms($body, $sender);
            } catch (Throwable $e) {
                $record->status = 'failed';
                $record->raw_json = ['error' => $e->getMessage()];
                $record->save();
                $summary['failed']++;

                continue;
            }

            $record->fill([
                'amount' => $parsed['amount'],
                'merchant' => $parsed['merchant'],
                'direction' => $parsed['direction'],
                'is_transaction' => $parsed['is_transaction'],
                'raw_json' => $parsed['raw'],
            ]);

            $isSpend = $parsed['is_transaction']
                && $parsed['direction'] !== 'credit'
                && (float) ($parsed['amount'] ?? 0) > 0;

            if ($isSpend) {
                $isFuel = $parsed['is_fuel'] || Fuel::looksLikeFuel($parsed['merchant']);
                $targetList = ($isFuel && $carList) ? $carList : $fallbackList;

                $entry = Entry::create([
                    'spending_list_id' => $targetList->id,
                    'category_id' => $isFuel ? $fuelCategoryId : null,
                    'item_name' => $parsed['merchant'] ?: 'Card transaction',
                    'amount' => $parsed['amount'],
                    'quantity' => 1,
                    'purchased_at' => $this->safeDate($parsed['occurred_at']) ?? $receivedAt ?? now(),
                    'source' => Entry::SOURCE_SMS,
                    'notes' => 'Imported from SMS'.($sender ? " ({$sender})" : ''),
                ]);

                $record->matched_list_id = $targetList->id;
                $record->entry_id = $entry->id;
                $record->status = 'imported';
                $summary['transactions']++;
            } else {
                $record->status = 'ignored';
                $summary['ignored']++;
            }

            $record->save();
            $summary['imported']++;
            $summary['messages'][] = [
                'id' => $record->id,
                'sender' => $record->sender,
                'merchant' => $record->merchant,
                'amount' => $record->amount !== null ? (float) $record->amount : null,
                'status' => $record->status,
                'matched_list_id' => $record->matched_list_id,
                'entry_id' => $record->entry_id,
                'received_at' => $record->received_at?->toIso8601String(),
            ];
        }

        return response()->json($summary);
    }

    /** Recent imported bank messages (for review in the app). */
    public function index()
    {
        $messages = BankMessage::query()
            ->latest()
            ->limit(100)
            ->get()
            ->map(fn (BankMessage $m) => [
                'id' => $m->id,
                'sender' => $m->sender,
                'body' => $m->body,
                'merchant' => $m->merchant,
                'amount' => $m->amount !== null ? (float) $m->amount : null,
                'direction' => $m->direction,
                'status' => $m->status,
                'matched_list_id' => $m->matched_list_id,
                'entry_id' => $m->entry_id,
                'received_at' => $m->received_at?->toIso8601String(),
            ]);

        return response()->json(['data' => $messages]);
    }

    private function parseReceivedAt(mixed $value): ?Carbon
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            if (is_numeric($value)) {
                $number = (int) $value;

                // Heuristic: > 10^11 is almost certainly milliseconds.
                return $number > 100000000000
                    ? Carbon::createFromTimestampMs($number)
                    : Carbon::createFromTimestamp($number);
            }

            return Carbon::parse((string) $value);
        } catch (Throwable) {
            return null;
        }
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
}
