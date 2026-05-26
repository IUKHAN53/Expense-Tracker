<?php

namespace App\Support;

use App\Models\User;

/**
 * Resolves "who is the current tenant" for both AccountScope (read filtering)
 * and BelongsToAccount (auto-stamp on create).
 *
 * The current request's authenticated user is the source of truth — we read
 * it via `request()->user()` so each HTTP request gets a per-request
 * resolver (matters in tests where the AuthManager singleton would otherwise
 * leak a previous request's user across calls).
 *
 * CLI commands and seeders run without a request user. Callers that need to
 * impersonate (queue jobs, system tasks) can use `actAs()` to set the
 * context for the duration of a closure.
 */
class TenantContext
{
    private static ?User $override = null;

    /** Run $callback with the given user treated as the current tenant. */
    public static function actAs(?User $user, callable $callback): mixed
    {
        $previous = self::$override;
        self::$override = $user;
        try {
            return $callback();
        } finally {
            self::$override = $previous;
        }
    }

    public static function user(): ?User
    {
        if (self::$override) {
            return self::$override;
        }

        if (! app()->bound('request')) {
            return null;
        }

        $request = request();
        $user = $request?->user();

        return $user instanceof User ? $user : null;
    }
}
