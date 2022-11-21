<?php

namespace App;

use App\{Option, Product};
use Illuminate\Database\Eloquent\Model;

class OptionValue extends Model
{
    public $timestamps = false;

    protected $fillable = ['name', 'product_id', 'option_id'];

    /**
     * Option associated
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function option()
    {
        return $this->belongsTo(Option::class);
    }

    /**
     * Related products
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

}
