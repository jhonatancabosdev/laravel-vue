<?php

namespace FME\Ups;

use FME\Ups\UpsRepository;
use Illuminate\Support\ServiceProvider;
use FME\Ups\Commands\UpsIntegrationCommand;

class UpsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/ups.php' => config_path('ups.php'),
            ], 'config');
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/ups.php', 'ups');

        $this->app->singleton('UpsRepository', function () {
            return new UpsRepository();
        });
    }
}
