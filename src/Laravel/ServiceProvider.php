<?php

namespace Balfour\Omnisend\Laravel;

use Balfour\Omnisend\Omnisend;
use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        $this->publishes([__DIR__ . '/config.php' => config_path('omnisend.php')], 'config');
    }

    /**
     * Register any application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/config.php', 'omnisend');

        $this->app->bind(Omnisend::class, function () {
            $client = new Client();
            return new Omnisend(
                $client,
                config('omnisend.api_key')
            );
        });
    }
}
