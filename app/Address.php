<?php

namespace App;

use App\{Model, City, Order, Country, State};
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    use SoftDeletes;

    /**
     * Soft delete date
     * 
     * @var array
     */
    protected $dates = [
        'deleted_at'
    ];

    protected $hidden = [
        'order_id', 'status', 'updated_at', 'country_id', 'customer_id', 'deleted_at'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'city',
        'status',
        'zipcode',
        'city_id',
        'state_id',
        'order_id',
        'state_id',
        'address_1',
        'address_2',
        'country_id',
        'customer_id',
        'validated_response'
    ];

    /**
     * @return string
     */
    public function getAddress1Attribute()
    {
        return ucwords(strtolower($this->attributes['address_1'] ?? ''));
    }

    /**
     * @return string
     */
    public function getAddress2Attribute()
    {
        return ucwords(strtolower($this->attributes['address_2'] ?? ''));
    }

    /**
     * @return string
     */
    public function getCityAttribute()
    {
        return ucwords(strtolower($this->attributes['city'] ?? ''));
    }

    /**
     * Country associated
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * State associated
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function state()
    {
        return $this->belongsTo(State::class);
    }

    /**
     * City associated
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Order associated
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
