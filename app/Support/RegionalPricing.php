<?php

namespace App\Support;

use Illuminate\Http\Request;

/**
 * Marketing-page pricing in the visitor's local currency, with USD as the
 * base of truth. Schema-level prices (the PKR figures Stripe will eventually
 * charge) live elsewhere; this is for what people read on the landing.
 */
class RegionalPricing
{
    /** Country code that anchors all conversions. */
    public const BASE = 'US';

    /** Cookie name for the user's explicit override. */
    public const COOKIE = 'kharcha_cc';

    /**
     * Region table. Prices are hand-tuned per market for fairness, not a
     * straight FX conversion. They round to numbers that feel native locally
     * (PKR 499 not PKR 555.43; INR 149 not INR 165.20).
     */
    private const REGIONS = [
        'US' => ['name' => 'United States', 'currency' => 'USD', 'monthly' => 1.99,  'lifetime' => 29.99],
        'PK' => ['name' => 'Pakistan',      'currency' => 'PKR', 'monthly' => 499,   'lifetime' => 7999],
        'IN' => ['name' => 'India',         'currency' => 'INR', 'monthly' => 149,   'lifetime' => 2499],
        'BD' => ['name' => 'Bangladesh',    'currency' => 'BDT', 'monthly' => 199,   'lifetime' => 3299],
        'LK' => ['name' => 'Sri Lanka',     'currency' => 'LKR', 'monthly' => 599,   'lifetime' => 9999],
        'AE' => ['name' => 'UAE',           'currency' => 'AED', 'monthly' => 7.49,  'lifetime' => 119],
        'GB' => ['name' => 'United Kingdom','currency' => 'GBP', 'monthly' => 1.49,  'lifetime' => 24.99],
    ];

    /**
     * Detect the visitor's region using a layered fallback chain.
     * Caller is responsible for writing the cookie when ?cc= is present.
     */
    public static function detect(Request $request): array
    {
        $candidates = [
            strtoupper((string) $request->query('cc', '')),
            strtoupper((string) $request->cookie(self::COOKIE, '')),
            strtoupper((string) $request->header('CF-IPCountry', '')),
            self::countryFromAcceptLanguage((string) $request->header('Accept-Language', '')),
        ];

        foreach ($candidates as $cc) {
            if ($cc && isset(self::REGIONS[$cc])) {
                return self::pack($cc);
            }
        }

        return self::pack(self::BASE);
    }

    /** Return the chosen region as a render-ready array. */
    public static function pack(string $cc): array
    {
        $r = self::REGIONS[$cc] ?? self::REGIONS[self::BASE];
        $effective = isset(self::REGIONS[$cc]) ? $cc : self::BASE;

        return [
            'cc'        => $effective,
            'name'      => $r['name'],
            'currency'  => $r['currency'],
            'monthly'   => $r['monthly'],
            'lifetime'  => $r['lifetime'],
            'monthly_display'  => self::format($r['monthly']),
            'lifetime_display' => self::format($r['lifetime']),
            'breakeven_months' => max(1, (int) round($r['lifetime'] / max($r['monthly'], 0.01))),
        ];
    }

    /** Full table of region records for the picker. */
    public static function all(): array
    {
        $out = [];
        foreach (array_keys(self::REGIONS) as $cc) {
            $out[$cc] = self::pack($cc);
        }

        return $out;
    }

    public static function isKnown(string $cc): bool
    {
        return isset(self::REGIONS[strtoupper($cc)]);
    }

    private static function format(float|int $amount): string
    {
        return floor($amount) == $amount
            ? number_format($amount, 0)
            : number_format($amount, 2);
    }

    /** "en-PK,en;q=0.9" => "PK" */
    private static function countryFromAcceptLanguage(string $header): string
    {
        if (! $header) {
            return '';
        }
        if (preg_match('/[a-zA-Z]{2}-([A-Z]{2})/', $header, $m)) {
            return strtoupper($m[1]);
        }

        return '';
    }
}
