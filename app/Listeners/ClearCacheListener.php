<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\ResponseCache\Facades\ResponseCache;

class ClearCacheListener
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event = null)
    {
        Artisan::call('cache:clear');
        Artisan::call('view:clear');
        // ResponseCache::clear();
    }
}