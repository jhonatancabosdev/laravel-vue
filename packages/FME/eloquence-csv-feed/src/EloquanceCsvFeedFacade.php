<?php

namespace FME\EloquenceCsvFeed;

use Illuminate\Support\Facades\Facade;

class EloquanceCsvFeedFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'EloquanceCsvFeed';
    }
}
