<?php

namespace App\Filament\SuperAdmin\Widgets;

use App\Models\Account;
use App\Models\Entry;
use App\Models\Receipt;
use App\Models\User;
use Carbon\CarbonImmutable;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * Headline platform metrics across every tenant. Rendered for SuperAdmins, so
 * the AccountScope on Entry/Receipt is bypassed and these aggregate globally.
 */
class PlatformOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 0;

    protected ?string $pollingInterval = null;

    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $monthStart = CarbonImmutable::now()->startOfMonth();

        $accounts = Account::query()->count();
        $accountsThisMonth = Account::query()->where('created_at', '>=', $monthStart)->count();

        $users = User::query()->count();
        $usersThisMonth = User::query()->where('created_at', '>=', $monthStart)->count();
        $verified = User::query()->whereNotNull('email_verified_at')->count();
        $verifiedPct = $users > 0 ? round($verified / $users * 100) : 0;

        $proLifetime = Account::query()->where('plan', Account::PLAN_PRO_LIFETIME)->count();
        $proMonthly = Account::query()
            ->where('plan', Account::PLAN_PRO_MONTHLY)
            ->where(fn ($q) => $q->whereNull('plan_expires_at')->orWhere('plan_expires_at', '>', now()))
            ->count();
        $proTotal = $proLifetime + $proMonthly;

        // MRR estimate uses the USD base price ($1.99/mo). Lifetime is one-off
        // revenue, not recurring, so it is reported separately, not in MRR.
        $mrr = $proMonthly * 1.99;

        $scansThisMonth = Receipt::query()->where('created_at', '>=', $monthStart)->count();

        $entriesTotal = Entry::query()->count();
        $spendVolume = (float) Entry::query()->sum('amount');

        return [
            Stat::make('Accounts', number_format($accounts))
                ->description('+'.$accountsThisMonth.' this month')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('primary'),

            Stat::make('Users', number_format($users))
                ->description('+'.$usersThisMonth.' this month · '.$verifiedPct.'% verified')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),

            Stat::make('Pro accounts', number_format($proTotal))
                ->description($proMonthly.' monthly · '.$proLifetime.' lifetime')
                ->descriptionIcon('heroicon-m-star')
                ->color('success'),

            Stat::make('Est. MRR', '$'.number_format($mrr, 2))
                ->description('from active monthly plans')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Scans this month', number_format($scansThisMonth))
                ->description('receipts uploaded for AI parsing')
                ->descriptionIcon('heroicon-m-camera')
                ->color('warning'),

            Stat::make('Spend volume', number_format($spendVolume))
                ->description(number_format($entriesTotal).' entries · mixed currency')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('gray'),
        ];
    }
}
