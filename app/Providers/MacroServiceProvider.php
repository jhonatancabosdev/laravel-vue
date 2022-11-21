<?php

namespace App\Providers;

use App\Services\Macros;
use Illuminate\Support\Collection;
use App\Macros\Collection\Paginate;
use Illuminate\Support\ServiceProvider;

/**
 * Class MacroServiceProvider
 * 
 * @package App\Providers
 */
class MacroServiceProvider extends ServiceProvider
{
	/**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Collection::macro('paginate', (new Paginate)());
    }
}