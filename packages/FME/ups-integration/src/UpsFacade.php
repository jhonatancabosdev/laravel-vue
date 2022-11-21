<?php

namespace FME\Ups;

use Illuminate\Support\Facades\Facade;

/**
 * @see \FME\Ups\UpsRepository
 */
class UpsFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'UpsRepository';
    }
}
