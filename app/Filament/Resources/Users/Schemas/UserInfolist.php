<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\Account;
use App\Models\Entry;
use App\Models\Receipt;
use App\Models\User;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

/**
 * SuperAdmin-facing user profile. Summarises who the user is, the household
 * they belong to and that household's plan/quota, plus the volume of records
 * this specific user has created. The activity counts query across tenants
 * (the viewer is a SuperAdmin, so AccountScope is bypassed).
 */
class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Identity')
                ->columns(2)
                ->schema([
                    TextEntry::make('name'),
                    TextEntry::make('email')->copyable(),
                    TextEntry::make('created_at')->label('Joined')->dateTime('d M Y, H:i'),
                    IconEntry::make('email_verified_at')
                        ->label('Email verified')
                        ->boolean(),
                    IconEntry::make('is_super_admin')
                        ->label('SuperAdmin')
                        ->boolean(),
                ]),

            Section::make('Household')
                ->columns(3)
                ->schema([
                    TextEntry::make('account.name')->label('Name')->placeholder('—'),
                    TextEntry::make('account.plan')
                        ->label('Plan')
                        ->badge()
                        ->color(fn (?string $state): string => match ($state) {
                            Account::PLAN_PRO_LIFETIME => 'success',
                            Account::PLAN_PRO_MONTHLY => 'info',
                            default => 'gray',
                        })
                        ->placeholder('—'),
                    TextEntry::make('account.currency')->label('Currency')->placeholder('—'),
                    TextEntry::make('account.plan_expires_at')
                        ->label('Plan expires')
                        ->dateTime('d M Y')
                        ->placeholder('—'),
                    TextEntry::make('members')
                        ->label('Members')
                        ->state(fn (User $record) => $record->account?->users()->count() ?? 0),
                    TextEntry::make('scans')
                        ->label('Scans this month')
                        ->state(fn (User $record) => $record->account
                            ? $record->account->scansThisMonth().' / '.($record->account->isPro() ? '∞' : Account::FREE_SCANS_PER_MONTH)
                            : '—'),
                ]),

            Section::make('Activity by this user')
                ->columns(3)
                ->schema([
                    TextEntry::make('entries_count')
                        ->label('Entries created')
                        ->state(fn (User $record) => Entry::query()->where('created_by_user_id', $record->id)->count()),
                    TextEntry::make('entries_sum')
                        ->label('Spend logged')
                        ->state(fn (User $record) => number_format((float) Entry::query()->where('created_by_user_id', $record->id)->sum('amount'))
                            .' '.($record->account?->currency ?? '')),
                    TextEntry::make('receipts_count')
                        ->label('Receipts scanned')
                        ->state(fn (User $record) => Receipt::query()->where('created_by_user_id', $record->id)->count()),
                ]),
        ]);
    }
}
