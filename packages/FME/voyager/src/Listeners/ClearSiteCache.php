<?php

namespace TCG\Voyager\Listeners;

use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Events\BreadAdded;
use Illuminate\Support\Facades\Artisan;
use Spatie\ResponseCache\Facades\ResponseCache;

class ClearSiteCache
{
    /**
     * Clear site cache
     *
     * @param BreadAdded $event
     *
     * @return void
     */
    public function handle($event)
    {
        try {
            if (app(ResponseCache::class)) {
                ResponseCache::clear();
            }
        } catch (\Exception $e) {
            logger($e->getMessage());
        }
        
        try {
            $event->dataType->model->flushCache();
        } catch (\Exception $e) {
            logger($e->getMessage());
        }
    }
}
