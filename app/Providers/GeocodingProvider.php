<?php

namespace App\Providers;

use App\Api\GeocoderApi;
use App\Services\GeocodingService;
use Illuminate\Support\ServiceProvider;

class GeocodingProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(GeocodingService::class, function ($app) {
            $config = $app['config']->get('geocoding');

            return new GeocodingService(
                key: $config['key'],
                api: $app->make(GeocoderApi::class),
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
