<?php

namespace App\Support;

use Illuminate\Http\Request;

/**
 * Marketing-page pricing in the visitor's local currency, with USD as the
 * fallback. Mirrors the Google Play prices; this is only what people read on the site.
 */
class RegionalPricing
{
    /** Country code that anchors all conversions. */
    public const BASE = 'US';

    /** Cookie name for the user's explicit override. */
    public const COOKIE = 'kharcha_cc';

    /**
     * Region table: the Kharcha Pro prices set in Google Play (kharcha_pro,
     * monthly + yearly base plans). Keep in sync with Play Console when prices change.
     */
    private const REGIONS = [
        'US' => ['name' => 'United States', 'currency' => 'USD', 'monthly' => 1.09, 'yearly' => 8.99],
        'PK' => ['name' => 'Pakistan',      'currency' => 'PKR', 'monthly' => 300,  'yearly' => 2500],
        'IN' => ['name' => 'India',         'currency' => 'INR', 'monthly' => 120,  'yearly' => 1000],
        'BD' => ['name' => 'Bangladesh',    'currency' => 'BDT', 'monthly' => 150,  'yearly' => 1300],
        'LK' => ['name' => 'Sri Lanka',     'currency' => 'LKR', 'monthly' => 350,  'yearly' => 3000],
        'AE' => ['name' => 'UAE',           'currency' => 'AED', 'monthly' => 4.19, 'yearly' => 34.99],
        'GB' => ['name' => 'United Kingdom','currency' => 'GBP', 'monthly' => 0.99, 'yearly' => 7.99],
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
            'yearly'    => $r['yearly'],
            'monthly_display' => self::format($r['monthly']),
            'yearly_display'  => self::format($r['yearly']),
            'yearly_saving'   => (int) round(100 * (1 - $r['yearly'] / ($r['monthly'] * 12))),
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
