<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

/**
 * SuperAdmin impersonation. start() stashes the real (SuperAdmin) user id in
 * the session and logs in as the target so the rest of the request stack —
 * including TenantContext / AccountScope — treats them as that user. stop()
 * restores the original session.
 *
 * The session key is the single source of truth for "am I impersonating?",
 * read by the return banner rendered on the /app and /admin panels.
 */
class Impersonation
{
    public const SESSION_KEY = 'impersonator_id';

    public static function isActive(): bool
    {
        return session()->has(self::SESSION_KEY);
    }

    public static function start(User $target): RedirectResponse
    {
        // Never nest impersonation: keep the original SuperAdmin id.
        if (! self::isActive()) {
            session()->put(self::SESSION_KEY, Auth::id());
        }

        Auth::login($target);

        return redirect()->to('/app');
    }

    public static function stop(): RedirectResponse
    {
        $originalId = session()->pull(self::SESSION_KEY);

        if ($originalId) {
            Auth::loginUsingId($originalId);

            return redirect()->to('/superadmin');
        }

        return redirect()->to('/app');
    }
}
