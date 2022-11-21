<?php

namespace FME\EloquenceCsvFeed;

class Helper
{
    public static function getModel($model)
    {
        return config('eloquanceCsvFeed.models')[$model]['namespace'];
    }

    public static function getHandler($model)
    {
        return config('eloquanceCsvFeed.models')[$model]['handler'];
    }
}
