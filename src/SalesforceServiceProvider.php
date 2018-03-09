<?php

namespace Midnite81\Salesforce;

use Illuminate\Support\ServiceProvider;
use Midnite81\Salesforce\Commands\GetToken;

class SalesforceServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/salesforce.php' => config_path('salesforce.php')
        ]);
        $this->commands([
            GetToken::class,
        ]);
    }
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/salesforce.php', 'salesforce');
    }
}