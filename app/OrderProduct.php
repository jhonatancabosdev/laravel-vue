<?php

namespace App;

use App\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderProduct extends Model
{
    use SoftDeletes;

    protected $table = 'order_product';

    /**
     * Fillable attributes
     *
     * @var array
     */
    protected $fillable = [
        'product_id', 'order_id', 'quantity', 'price',
    ];

    /**
     * @var boolean
     */
    public $incrementing = true;

    /**
     * @return belongsTo
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * @return belongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

