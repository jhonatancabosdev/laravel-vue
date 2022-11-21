<?php

namespace App;

use App\{Product, OptionValue};
use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    protected $fillable = ['name'];
    
    /**
     * Related products
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
    /**
     * Related values
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function values()
    {
        return $this->hasMany(OptionValue::class);
    }
}
