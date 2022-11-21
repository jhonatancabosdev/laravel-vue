<?php

namespace App;

use App\Order;
use App\Shipping;
use App\Product;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    protected $fillable = [
        'coupon_code',
        'is_active',
        'discount_type',
        'discount_amount',
        'discount_method',
        'expiration_date',
        'activation_date',
        'collects_email',
        'name',
        'description',
        'shipping_id',
        'trigger_amount',
        'is_triggerable',
    ];

    /**
    * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
    */
    public function order()
    {
        return $this->belongsToMany(Order::class);
    }
    
    /**
    * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
    */
    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    public function shipping()
    {
        return $this->belongsTo(Shipping::class);
    }

    public function getAmountAttribute()
    {
        if($this->discount_method === 'percentage') {
            return floatval($this->discount_amount) * 100 . '%';
        } elseif($this->discount_method === 'dollars') {
            return config('cart.currency_symbol') . number_format($this->discount_amount, 2);
        } else {
            return 'free items';
        }
         
    }


    // Get Discounted Product Prices

}