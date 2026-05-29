<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\AccountProvisioner;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Api\EmailVerificationController;

class AuthController extends Controller
{
    /** Create a new user + account and immediately return an API token. */
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'email' => ['required', 'email', 'max:120', 'unique:users,email'],
            'password' => ['required', 'string', PasswordRule::min(8)],
            'household_name' => ['nullable', 'string', 'max:80'],
            'device_name' => ['nullable', 'string', 'max:100'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => strtolower($data['email']),
            'password' => Hash::make($data['password']),
            'is_super_admin' => false,
        ]);

        AccountProvisioner::provision(
            $user,
            $data['household_name'] ?? ($data['name']."'s Household"),
        );

        // Send the 6-digit verification code so the inbox-wall screen in
        // the app has something to verify against. Wrapped in try/catch in
        // case SMTP is down; the user can request a re-send from the wall.
        try {
            EmailVerificationController::dispatchFor($user);
        } catch (\Throwable $e) {
            Log::warning('Verification dispatch failed', [
                'user_id' => $user->id, 'email' => $user->email, 'error' => $e->getMessage(),
            ]);
        }

        // The welcome email is intentionally NOT sent here. It goes out from
        // EmailVerificationController::verify once the address is confirmed —
        // no point welcoming an unverified (possibly mistyped) inbox, and it
        // keeps signup snappy by dropping one synchronous SMTP round-trip.

        $token = $user->createToken($data['device_name'] ?? 'mobile')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $this->presentUser($user->fresh('account')),
            'requires_email_verification' => true,
        ], 201);
    }

    /** Exchange email + password for a Sanctum API token. */
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:100'],
        ]);

        $user = User::where('email', strtolower($data['email']))->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken($data['device_name'] ?? 'mobile')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $this->presentUser($user->load('account')),
        ]);
    }

    /** Return the currently authenticated user (with their account + plan). */
    public function me(Request $request)
    {
        return response()->json([
            'user' => $this->presentUser($request->user()->load('account')),
        ]);
    }

    /** Revoke the token used for the current request. */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out.']);
    }

    /**
     * Send a password-reset link. Always returns 200 so the response cannot
     * be used to enumerate registered emails.
     */
    public function forgotPassword(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
        ]);

        Password::sendResetLink(['email' => strtolower($data['email'])]);

        return response()->json([
            'message' => 'If that email is registered, a reset link has been sent.',
        ]);
    }

    /** Consume a reset token and set the new password. */
    public function resetPassword(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'token' => ['required', 'string'],
            'password' => ['required', 'string', PasswordRule::min(8), 'confirmed'],
        ]);

        $status = Password::reset(
            [
                'email' => strtolower($data['email']),
                'password' => $data['password'],
                'password_confirmation' => $data['password_confirmation'] ?? $data['password'],
                'token' => $data['token'],
            ],
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                // Log every other device out — only the current session keeps its token.
                $user->tokens()->delete();

                event(new PasswordReset($user));
            },
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                'email' => [trans($status)],
            ]);
        }

        return response()->json(['message' => trans($status)]);
    }

    /** Serialise a user + their account/plan for API responses. */
    private function presentUser(User $user): array
    {
        $account = $user->account;

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'email_verified_at' => $user->email_verified_at?->toIso8601String(),
            'is_super_admin' => $user->isSuperAdmin(),
            'account' => $account ? [
                'id' => $account->id,
                'name' => $account->name,
                'plan' => $account->plan,
                'is_pro' => $account->isPro(),
                'scans_used_this_month' => $account->scansThisMonth(),
                'scans_free_quota' => \App\Models\Account::FREE_SCANS_PER_MONTH,
                'plan_expires_at' => $account->plan_expires_at?->toIso8601String(),
                'currency' => $account->currency,
                'requires_currency' => $account->currency === null,
            ] : null,
        ];
    }
}
