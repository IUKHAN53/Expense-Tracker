<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * API equivalent of Laravel's `verified` middleware. Instead of redirecting
 * unverified users to a Blade page (which makes no sense for the mobile
 * app), returns a 403 JSON body the app can branch on to show the OTP
 * screen.
 */
class EnsureEmailVerifiedJson
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ! $user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Please confirm your email address. Enter the 6-digit code we emailed you.',
                'requires_email_verification' => true,
                'email' => $user->email,
            ], 403);
        }

        return $next($request);
    }
}
