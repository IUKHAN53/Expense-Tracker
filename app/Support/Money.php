<?php

namespace App\Support;

/**
 * Server-side money formatting in an account's currency. Mirrors the mobile
 * app's currency symbol table so web reports read in the user's own currency
 * instead of a hardcoded "Rs".
 */
class Money
{
    private const SYMBOLS = [
        'USD' => '$', 'EUR' => '€', 'GBP' => '£', 'INR' => '₹', 'PKR' => 'Rs',
        'BDT' => '৳', 'LKR' => 'Rs', 'AED' => 'AED', 'SAR' => 'SAR',
        'CAD' => 'CA$', 'AUD' => 'A$', 'CNY' => '¥',
    ];

    public static function symbol(?string $code): string
    {
        return self::SYMBOLS[strtoupper((string) $code)] ?? (strtoupper((string) $code) ?: '$');
    }

    /** format(142860, 'PKR') -> "Rs 142,860". */
    public static function format(float|int|null $amount, ?string $code, int $decimals = 0): string
    {
        return self::symbol($code).' '.number_format((float) $amount, $decimals);
    }

    /** The currency of the currently authenticated user's account. */
    public static function current(): string
    {
        return strtoupper(auth()->user()?->account?->currency ?: 'USD');
    }
}
