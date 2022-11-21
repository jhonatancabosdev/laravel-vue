<?php

namespace App;

use App\{Model, Product};

class ProductImage extends Model
{
    /**
     * Fillable attributes
     * 
     * @var array
     */
    protected $fillable = [
        'product_id', 'src', 'is_main', 'is_transparent'
    ];

    /**
     * Related product
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class)
                    ->remember(self::getDefaultCacheTime());
    }
}
