<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Entry;
use App\Models\SpendingList;
use App\Models\User;
use Illuminate\Console\Command;

/**
 * One-off move to the standalone app: dumps one account's lists, categories
 * and entries as a Kharcha 2.0 snapshot (schema 1, unencrypted JSON) that the
 * phone's "Restore from a file" accepts.
 *
 *   php artisan kharcha:export-snapshot you@example.com --output=/root/kharcha.json
 *
 * The file is plain text: copy it to the phone, restore, then delete it.
 */
class ExportSnapshot extends Command
{
    protected $signature = 'kharcha:export-snapshot
        {email : Email of a user in the account to export}
        {--output= : Where to write the JSON (default: storage/app/kharcha-snapshot.json)}';

    protected $description = 'Export one account as a Kharcha 2.0 snapshot for the standalone app';

    public function handle(): int
    {
        $user = User::where('email', $this->argument('email'))->first();
        if (! $user || ! $user->account_id) {
            $this->error('No user with an account found for that email.');

            return self::FAILURE;
        }
        $accountId = $user->account_id;

        // CLI runs without a tenant, so AccountScope is a no-op: filter explicitly.
        $lists = SpendingList::withoutGlobalScopes()->where('account_id', $accountId)->orderBy('id')->get()
            ->map(fn (SpendingList $l) => [
                'id' => $l->id,
                'name' => $l->name,
                'type' => $l->type,
                'color' => $l->color,
                'icon' => $l->icon,
                'monthly_budget' => $l->monthly_budget !== null ? (float) $l->monthly_budget : null,
                'sort_order' => (int) $l->sort_order,
                'is_active' => (bool) $l->is_active,
            ]);

        $categories = Category::withoutGlobalScopes()->where('account_id', $accountId)->orderBy('id')->get()
            ->map(fn (Category $c) => [
                'id' => $c->id,
                'name' => $c->name,
                'icon' => $c->icon,
                'color' => $c->color,
            ]);

        $entries = Entry::withoutGlobalScopes()->where('account_id', $accountId)->orderBy('id')->get()
            ->map(fn (Entry $e) => [
                'spending_list_id' => $e->spending_list_id,
                'category_id' => $e->category_id,
                'item_name' => $e->item_name,
                'amount' => (float) $e->amount,
                'original_amount' => $e->original_amount !== null ? (float) $e->original_amount : null,
                'original_currency' => $e->original_currency,
                'fx_rate' => $e->fx_rate !== null ? (float) $e->fx_rate : null,
                'quantity' => (float) ($e->quantity ?? 1),
                'unit' => $e->unit,
                'purchased_at' => $e->purchased_at?->toIso8601String(),
                'source' => $e->source,
                'notes' => $e->notes,
                'fuel_liters' => $e->fuel_liters !== null ? (float) $e->fuel_liters : null,
                'fuel_rate' => $e->fuel_rate !== null ? (float) $e->fuel_rate : null,
                'odometer' => $e->odometer,
                'fuel_type' => $e->fuel_type,
                'is_full_tank' => $e->is_full_tank,
                'split_group_id' => $e->split_group_id,
            ]);

        $snapshot = [
            'app' => 'kharcha',
            'schema' => 1,
            'exported_at' => now()->toIso8601String(),
            'currency' => $user->account?->currency,
            'lists' => $lists,
            'categories' => $categories,
            'entries' => $entries,
        ];

        $path = $this->option('output') ?: storage_path('app/kharcha-snapshot.json');
        file_put_contents($path, json_encode($snapshot, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));
        @chmod($path, 0600);

        $fuel = $entries->whereNotNull('fuel_liters')->count();
        $this->info("Wrote {$path}");
        $this->line("Lists: {$lists->count()}   Categories: {$categories->count()}   Entries: {$entries->count()} ({$fuel} refills)");
        $this->warn('This file is NOT encrypted. Delete it once it is on your phone.');

        return self::SUCCESS;
    }
}
