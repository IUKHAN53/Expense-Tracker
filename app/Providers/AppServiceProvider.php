<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Reset-password emails point at the Blade page on this host instead
        // of Laravel's default /password/reset/{token} route.
        ResetPassword::createUrlUsing(function ($notifiable, string $token) {
            return url('/reset-password').'?'.http_build_query([
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ]);
        });
    }
}
