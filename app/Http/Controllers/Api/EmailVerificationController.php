<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\EmailVerificationCodeMail;
use App\Mail\WelcomeMail;
use App\Models\EmailVerificationCode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class EmailVerificationController extends Controller
{
    /** Authenticated: confirm with a 6-digit code from email. */
    public function verify(Request $request)
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email is already verified.']);
        }

        $record = EmailVerificationCode::query()
            ->where('email', $user->email)
            ->whereNull('consumed_at')
            ->latest('id')
            ->first();

        if (! $record) {
            throw ValidationException::withMessages([
                'code' => ['No active code. Request a new one.'],
            ]);
        }

        if ($record->isExpired()) {
            throw ValidationException::withMessages([
                'code' => ['That code has expired. Request a new one.'],
            ]);
        }

        if ($record->attempts >= 5) {
            throw ValidationException::withMessages([
                'code' => ['Too many wrong attempts. Request a new code.'],
            ]);
        }

        if (! hash_equals($record->code, $data['code'])) {
            $record->increment('attempts');
            throw ValidationException::withMessages([
                'code' => ['Wrong code. '.(5 - $record->attempts).' attempts left.'],
            ]);
        }

        $record->forceFill(['consumed_at' => now()])->save();
        $user->forceFill(['email_verified_at' => now()])->save();

        // Welcome the user now that we know the address is real. Mail failures
        // must never block a successful verification.
        try {
            Mail::to($user->email)->send(new WelcomeMail($user->fresh('account')));
        } catch (\Throwable $e) {
            Log::warning('Welcome email failed to send', [
                'user_id' => $user->id,
                'email'   => $user->email,
                'error'   => $e->getMessage(),
            ]);
        }

        return response()->json([
            'message' => 'Email verified.',
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at->toIso8601String(),
            ],
        ]);
    }

    /** Authenticated: send (or re-send) the 6-digit code. */
    public function resend(Request $request)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email is already verified.']);
        }

        return $this->dispatchCode($user);
    }

    /**
     * Helper invoked from AuthController::register so signup sends the
     * first code as part of the same transaction.
     */
    public static function dispatchFor(User $user)
    {
        return (new self)->dispatchCode($user);
    }

    private function dispatchCode(User $user)
    {
        $record = EmailVerificationCode::create([
            'email'      => $user->email,
            'code'       => EmailVerificationCode::newCode(),
            'expires_at' => now()->addMinutes(15),
        ]);

        try {
            Mail::to($user->email)->send(new EmailVerificationCodeMail($record->code, $user->name));
        } catch (\Throwable $e) {
            Log::warning('Verification email failed', [
                'user_id' => $user->id,
                'email'   => $user->email,
                'error'   => $e->getMessage(),
            ]);
        }

        return response()->json([
            'message' => 'A 6-digit code has been sent to '.$user->email.'. It expires in 15 minutes.',
            'expires_at' => $record->expires_at->toIso8601String(),
        ], 201);
    }
}
