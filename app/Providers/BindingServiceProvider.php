<?php

namespace App\Providers;

use App\Repositories\CartRepository;
use App\Repositories\DiscountRepository;
use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\CartRepositoryContract;
use App\Repositories\Contracts\DiscountRepositoryContract;

class BindingServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(CartRepositoryContract::class, CartRepository::class);
        $this->app->singleton(DiscountRepositoryContract::class, DiscountRepository::class);
    }
}
