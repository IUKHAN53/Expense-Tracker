<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Services\GeminiService;
use App\Services\PlayBilling;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Stateless receipt scan for the standalone 2.x app (no accounts).
 * The photo is parsed in memory and never stored. Pro subscribers are
 * verified with Google Play; everyone else gets FREE_SCANS per install.
 */
class ScanController extends Controller
{
    public const FREE_SCANS = 3;

    public function __invoke(Request $request, GeminiService $gemini, PlayBilling $play)
    {
        $data = $request->validate([
            'image' => ['required', 'image', 'max:8192'],
            'device_id' => ['required', 'uuid'],
            'purchase_token' => ['nullable', 'string', 'max:1024'],
            'currency' => ['nullable', 'string', 'regex:/^[A-Z]{3}$/'],
            'categories' => ['nullable', 'array', 'max:40'],
            'categories.*' => ['string', 'max:40'],
        ]);

        // shortcut: a reinstall gets a new device_id and 3 more free scans; add Play Integrity if abused.
        $hash = hash('sha256', $data['device_id']);
        DB::table('scan_devices')->insertOrIgnore(['device_hash' => $hash, 'created_at' => now(), 'updated_at' => now()]);
        $device = DB::table('scan_devices')->where('device_hash', $hash)->first();

        $pro = ! empty($data['purchase_token']) && $play->isActivePro($data['purchase_token']);
        $freeLeft = max(0, self::FREE_SCANS - $device->free_used);

        if (! $pro && $freeLeft === 0) {
            return response()->json([
                'code' => 'paywall',
                'message' => 'You have used your '.self::FREE_SCANS.' free scans. Get Kharcha Pro for unlimited scans.',
                'pro' => false,
                'free_left' => 0,
            ], 402);
        }

        $file = $request->file('image');
        try {
            $parsed = $gemini->parseReceipt(
                $file->getRealPath(),
                $file->getMimeType(),
                $data['currency'] ?? 'PKR',
                $data['categories'] ?? null,
            );
        } catch (Throwable $e) {
            report($e);

            // A failed read never uses up a free scan.
            return response()->json([
                'code' => 'unreadable',
                'message' => 'Couldn’t read that receipt. Try a sharper, well-lit photo.',
                'pro' => $pro,
                'free_left' => $freeLeft,
            ], 422);
        }

        DB::table('scan_devices')->where('device_hash', $hash)
            ->increment($pro ? 'pro_used' : 'free_used', 1, ['updated_at' => now()]);

        unset($parsed['raw']);

        return response()->json([
            'scan' => $parsed,
            'pro' => $pro,
            'free_left' => $pro ? $freeLeft : $freeLeft - 1,
        ]);
    }
}
