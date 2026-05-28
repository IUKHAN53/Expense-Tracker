<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Models\CurrencyRate;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

/**
 * Pull the latest USD-based FX rates from the free ExchangeRate-API endpoint
 * and upsert one row per supported currency. Scheduled daily (see
 * routes/console.php) and run once at deploy so the table is never empty.
 */
class RefreshFxRates extends Command
{
    protected $signature = 'fx:refresh';

    protected $description = 'Refresh USD-pivoted currency rates from the FX provider';

    public function handle(): int
    {
        $response = Http::timeout(20)->retry(2, 1500)->acceptJson()
            ->get('https://open.er-api.com/v6/latest/USD');

        if (! $response->ok()) {
            $this->error('FX provider returned HTTP '.$response->status());

            return self::FAILURE;
        }

        $json = $response->json();
        $rates = $json['rates'] ?? [];

        if (($json['result'] ?? null) !== 'success' || empty($rates)) {
            $this->error('FX provider response was not usable.');

            return self::FAILURE;
        }

        $fetchedAt = isset($json['time_last_update_unix'])
            ? Carbon::createFromTimestamp((int) $json['time_last_update_unix'])
            : now();

        $written = 0;
        foreach (Account::SUPPORTED_CURRENCIES as $code) {
            $rate = $code === 'USD' ? 1.0 : ($rates[$code] ?? null);
            if ($rate === null) {
                $this->warn("No rate for {$code} — skipped.");

                continue;
            }

            CurrencyRate::updateOrCreate(
                ['base' => 'USD', 'quote' => $code],
                ['rate' => $rate, 'fetched_at' => $fetchedAt],
            );
            $written++;
        }

        $this->info("Refreshed {$written} currency rates (as of {$fetchedAt->toDateString()}).");

        return self::SUCCESS;
    }
}
