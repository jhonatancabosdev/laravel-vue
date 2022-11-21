<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Carrier extends Model
{
	use SoftDeletes;

	/**
	 * @var array
	 */
    protected $fillable = [
    	'order_id',
		'service_name',
		'service_code',
		'shipment_cost',
		'other_cost',
		'carrier_code',
    ];

    /**
     * @return Builder
     */
    public function order()
    {
    	$this->belongsTo(Order::class);
    }
}
