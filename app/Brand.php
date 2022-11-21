<?php

namespace App;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    use SoftDeletes;
    
    /**
     * Fillable attributes
     * 
     * @var Array
     */
	protected $fillable = [
        'name', 
        'slug',
        'cover',
        'status'
    ];

    /**
     * All related products
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}

