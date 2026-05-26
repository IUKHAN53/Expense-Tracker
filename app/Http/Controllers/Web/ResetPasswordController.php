<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;

class ResetPasswordController extends Controller
{
    public function show(Request $request)
    {
        $token = (string) $request->query('token', '');
        $email = (string) $request->query('email', '');

        $state = ($token !== '' && filter_var($email, FILTER_VALIDATE_EMAIL))
            ? 'form'
            : 'expired';

        return view('auth.reset-password', compact('token', 'email', 'state'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'confirmed', PasswordRule::min(8)],
        ]);

        $status = Password::reset(
            [
                'email' => strtolower($data['email']),
                'password' => $data['password'],
                'password_confirmation' => $data['password'],
                'token' => $data['token'],
            ],
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                // Sign every device out — a fresh login proves possession of the new password.
                $user->tokens()->delete();

                event(new PasswordReset($user));
            },
        );

        if ($status === Password::PASSWORD_RESET) {
            return view('auth.reset-password', [
                'token' => $data['token'],
                'email' => $data['email'],
                'state' => 'success',
            ]);
        }

        if (in_array($status, [Password::INVALID_TOKEN, Password::INVALID_USER], true)) {
            return view('auth.reset-password', [
                'token' => $data['token'],
                'email' => $data['email'],
                'state' => 'expired',
            ]);
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['password' => trans($status)]);
    }
}
