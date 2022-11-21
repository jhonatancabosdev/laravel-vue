<?php 

namespace App;

use Watson\Rememberable\Rememberable;
use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * The Easy way to use cachable queries
 */
abstract class Model extends Eloquent
{
    use Rememberable;
    
    /**
     * Default models cache life time
     * 
     * @return int
     */
    public static function getDefaultCacheTime()
    {
        return config('default-variables.cache_life_time');
    }
}

