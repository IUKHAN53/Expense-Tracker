<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\Impersonation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ImpersonationController extends Controller
{
    /**
     * Begin impersonating $user. Only a SuperAdmin may do this, and only for a
     * different user who actually has a household to land in.
     */
    public function start(User $user): RedirectResponse
    {
        $current = Auth::user();

        abort_unless($current?->isSuperAdmin(), 403);

        if ($user->id === $current->id || ! $user->account_id) {
            throw new AccessDeniedHttpException('Cannot impersonate this user.');
        }

        return Impersonation::start($user);
    }

    /** Return to the original SuperAdmin session. */
    public function leave(): RedirectResponse
    {
        return Impersonation::stop();
    }
}
