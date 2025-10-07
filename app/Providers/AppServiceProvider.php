<?php

namespace App\Providers;

use App\Models\MarketingEmail;
use App\Models\Practice;
use App\Models\Practitioner;
use App\Models\SalesRep;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void {}

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Relation::enforceMorphMap([
            'tenant' => Tenant::class,
            'practice' => Practice::class,
            'practitioner' => Practitioner::class,
            'marketing_email' => MarketingEmail::class,
            'sales_rep' => SalesRep::class,
            'user' => User::class,
        ]);
    }
}
