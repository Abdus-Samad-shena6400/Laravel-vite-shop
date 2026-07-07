<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\ResetPassword; // <--- Import ResetPassword notification

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Customize the password reset link URL to point to the Vue SPA
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return 'http://localhost:5173/reset-password/' . $token . '?email=' . urlencode($notifiable->getEmailForPasswordReset());
        });
    }
}
