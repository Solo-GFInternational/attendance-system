<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
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
        $httpsPort = config('app.https_proxy_port');

        if ($httpsPort && ! $this->app->runningInConsole()
            && (int) request()->getPort() === (int) $httpsPort) {
            URL::forceScheme('https');
        }
    }
}
