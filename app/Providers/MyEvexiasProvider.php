<?php

namespace App\Providers;

use App\Api\PracticeApi;
use App\Services\PracticeService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class MyEvexiasProvider extends ServiceProvider
{
    public function register()
    {
        app()->bind(PracticeService::class, function (Application $app) {
            $config = $app['config']->get('myevexias');

            return new PracticeService(
                $config['key'],
                $app->make(PracticeApi::class),
            );
        });
    }
}