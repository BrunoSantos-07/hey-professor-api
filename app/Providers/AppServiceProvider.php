<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

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
        Model::unguard();

        ResetPassword::createUrlUsing(fn (object $notifiable, string $token) => config('app.frontend_url') . "/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}");
    }
}
