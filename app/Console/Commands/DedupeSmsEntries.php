<?php

namespace App\Console\Commands;

use App\Models\BankMessage;
use App\Models\Entry;
use App\Support\EntryDeduper;
use Illuminate\Console\Command;

/**
 * Remove SMS-imported entries that duplicate a manual entry or a scanned
 * receipt. Idempotent — re-running finds nothing new. Use --dry-run to
 * preview without touching anything.
 */
class DedupeSmsEntries extends Command
{
    protected $signature = 'kharcha:dedupe-sms {--dry-run : Only report, do not delete}';

    protected $description = 'Find SMS-imported entries that duplicate a manual entry or scanned receipt and remove them.';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $removed = 0;

        $smsEntries = Entry::query()
            ->where('source', Entry::SOURCE_SMS)
            ->orderBy('id')
            ->get();

        foreach ($smsEntries as $sms) {
            $amount = (float) $sms->amount;
            $when = $sms->purchased_at ?? $sms->created_at;

            // Another entry on the same list, same amount, same window —
            // but NOT another SMS entry (we don't want SMS cannibalising itself).
            $other = Entry::query()
                ->where('id', '!=', $sms->id)
                ->where('spending_list_id', $sms->spending_list_id)
                ->where('source', '!=', Entry::SOURCE_SMS)
                ->whereBetween('purchased_at', [
                    $when->copy()->subHours(EntryDeduper::WINDOW_HOURS),
                    $when->copy()->addHours(EntryDeduper::WINDOW_HOURS),
                ])
                ->whereRaw('ABS(CAST(amount AS REAL) - ?) < 1', [$amount])
                ->orderBy('id')
                ->first();

            $receipt = $other ? null : EntryDeduper::findDuplicateReceipt($amount, $when);

            if (! $other && ! $receipt) {
                continue;
            }

            $linkId = $other?->id ?? $receipt?->entries()->orderBy('id')->value('id');
            $what = $other
                ? "entry #{$other->id}"
                : "receipt #{$receipt->id} (split into ".$receipt->entries()->count().' items)';

            $this->line(sprintf(
                ' [%s] SMS #%d "%s" Rs %s → %s',
                $dryRun ? 'WOULD REMOVE' : 'REMOVE',
                $sms->id,
                $sms->item_name,
                number_format($amount),
                $what,
            ));

            $removed++;

            if ($dryRun) {
                continue;
            }

            // Point the BankMessage at the surviving entry, mark as duplicate.
            BankMessage::where('entry_id', $sms->id)->update([
                'entry_id' => $linkId,
                'status' => 'duplicate',
            ]);
            $sms->delete();
        }

        $verb = $dryRun ? 'Would remove' : 'Removed';
        $this->info("{$verb} {$removed} duplicate SMS ".str('entry')->plural($removed).'.');

        return self::SUCCESS;
    }
}
