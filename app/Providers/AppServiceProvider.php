<?php

namespace App\Providers;

use App\Models\User;
use App\Services\SmsManager;
use Illuminate\Support\Facades\Gate;
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
        Gate::define('use-sms', function (User $user) {
            $smsManager = app(SmsManager::class);
            return $smsManager->getCredits($user) > 0;
        });

        Gate::define('notify', function (User $user, string $method) {
            if ($method !== 'sms') {
                return true;
            }
            return Gate::allows('use-sms');
        });
    }
}
