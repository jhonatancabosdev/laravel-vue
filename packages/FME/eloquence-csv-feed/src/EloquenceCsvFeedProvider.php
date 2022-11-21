<?php

namespace FME\EloquenceCsvFeed;

use Illuminate\Support\ServiceProvider;
use FME\EloquenceCsvFeed\EloquanceCsvFeedController;

class EloquenceCsvFeedProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->mergeConfigFrom(
            str_replace('/src', '', __DIR__) . '/config/config.php',
            'eloquanceCsvFeed'
        );

        $this->loadRoutesFrom(
            str_replace('/src', '', __DIR__) .'/routes/api.php'
        );
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('EloquanceCsvFeedController', function () {
            return new EloquanceCsvFeedController();
        });
    }
}
