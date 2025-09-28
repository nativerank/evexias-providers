<?php

namespace App\Providers;

use App\Api\PlacesApi;
use App\Services\PlacesService;
use Illuminate\Support\ServiceProvider;

class GeocodingProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(PlacesService::class, function ($app) {
            $config = $app['config']->get('places');

            return new PlacesService(
                key: $config['key'],
                api: $app->make(PlacesApi::class),
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
