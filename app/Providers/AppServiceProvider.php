<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use OpenAI;
use OpenAI\Client;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->singleton(Client::class, function () {
            return OpenAI::client(config('app.token'));
        });
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }
}
