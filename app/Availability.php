<?php

namespace App;

use App\{Model, Product};
use Illuminate\Support\Collection;

class Availability extends Model
{
    /**
     * Fillable attributes
     * 
     * @var Array
     */
    protected $fillable = [
        'status',
        'name',
    ];

    /**
     * All related products
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products()
    {
        return $this->hasMany(Product::class)
                    ->remember(self::getDefaultCacheTime());
    }
}

