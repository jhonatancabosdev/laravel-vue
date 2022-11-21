<?php

namespace App;

use App\Model;
use App\Custom\ShoppingCart;

class Shipping extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'base_cost', 'status'
    ];

    /**
     * Get shipping cost
     *
     * @return float|null
     */
    public function getCostAttribute(): ?float
    {
        return $this->base_cost;
    }
}
