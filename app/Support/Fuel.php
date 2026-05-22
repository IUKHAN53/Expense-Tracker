<?php

namespace App\Support;

/**
 * Keyword fallback for detecting fuel/petrol purchases when the AI
 * classification is unavailable or uncertain. The AI's own flag
 * (receipt_type=fuel / is_fuel) is always the primary signal.
 */
class Fuel
{
    /** Lowercase substrings that identify a Pakistani fuel/CNG merchant. */
    private const KEYWORDS = [
        'pso', 'shell', 'attock', 'hascol', 'byco', 'caltex',
        'total parco', 'totalparco', 'total energies', 'totalenergies',
        'puma energy', 'go petroleum', 'petrol', 'diesel', 'cng',
        'filling station', 'fuel station', 'petroleum', 'petrol pump',
    ];

    public static function looksLikeFuel(?string ...$values): bool
    {
        $haystack = strtolower(trim(implode(' ', array_filter($values, fn ($v) => $v !== null && $v !== ''))));

        if ($haystack === '') {
            return false;
        }

        foreach (self::KEYWORDS as $keyword) {
            if (str_contains($haystack, $keyword)) {
                return true;
            }
        }

        return false;
    }
}
