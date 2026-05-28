<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Tenant boundary. Every SpendingList, Entry, Receipt and Category belongs to
 * exactly one Account; a User belongs to exactly one Account. SuperAdmins
 * bypass account scoping and see everything in the /superadmin Filament panel.
 */
class Account extends Model
{
    public const PLAN_FREE = 'free';
    public const PLAN_PRO_MONTHLY = 'pro_monthly';
    public const PLAN_PRO_LIFETIME = 'pro_lifetime';

    /** Free-tier monthly cap on AI receipt scans. */
    public const FREE_SCANS_PER_MONTH = 3;

    /** Per-plan household member caps. Counts the owner plus invited users. */
    public const FREE_MAX_MEMBERS = 3;

    public const PRO_MAX_MEMBERS = 5;

    /** Currencies the app understands. Add new codes here as needed. */
    public const SUPPORTED_CURRENCIES = [
        'USD', 'EUR', 'GBP', 'INR', 'PKR', 'BDT', 'LKR',
        'AED', 'SAR', 'CAD', 'AUD', 'CNY',
    ];

    protected $fillable = [
        'name',
        'plan',
        'plan_expires_at',
        'scans_used_this_month',
        'scans_reset_at',
        'currency',
    ];

    protected function casts(): array
    {
        return [
            'plan_expires_at' => 'datetime',
            'scans_reset_at' => 'date',
            'scans_used_this_month' => 'integer',
        ];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function spendingLists(): HasMany
    {
        return $this->hasMany(SpendingList::class);
    }

    public function entries(): HasMany
    {
        return $this->hasMany(Entry::class);
    }

    public function receipts(): HasMany
    {
        return $this->hasMany(Receipt::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function isPro(): bool
    {
        if ($this->plan === self::PLAN_PRO_LIFETIME) {
            return true;
        }
        if ($this->plan === self::PLAN_PRO_MONTHLY) {
            return $this->plan_expires_at && $this->plan_expires_at->isFuture();
        }

        return false;
    }

    /**
     * Scans used in the current calendar month. Lazily resets the counter
     * when a new month starts so we never need a cron job.
     */
    public function scansThisMonth(): int
    {
        $monthStart = CarbonImmutable::now()->startOfMonth();

        if (! $this->scans_reset_at || $this->scans_reset_at->lt($monthStart)) {
            $this->forceFill([
                'scans_used_this_month' => 0,
                'scans_reset_at' => $monthStart,
            ])->save();
        }

        return (int) $this->scans_used_this_month;
    }

    public function canScanReceipt(): bool
    {
        return $this->isPro() || $this->scansThisMonth() < self::FREE_SCANS_PER_MONTH;
    }

    public function recordScan(): void
    {
        // Touch `scansThisMonth` first to roll the counter at month boundaries.
        $this->scansThisMonth();
        $this->increment('scans_used_this_month');
    }

    /** Member cap depends on the household's plan. */
    public function maxMembers(): int
    {
        return $this->isPro() ? self::PRO_MAX_MEMBERS : self::FREE_MAX_MEMBERS;
    }

    /** Live count = users on the account + unaccepted, unexpired invites. */
    public function memberUsage(): int
    {
        return $this->users()->count() + $this->pendingInvitations()->count();
    }

    public function canInviteMore(): bool
    {
        return $this->memberUsage() < $this->maxMembers();
    }

    public function pendingInvitations()
    {
        return $this->hasMany(AccountInvitation::class)
            ->whereNull('accepted_at')
            ->where('expires_at', '>', now());
    }

    public function invitations()
    {
        return $this->hasMany(AccountInvitation::class);
    }
}
