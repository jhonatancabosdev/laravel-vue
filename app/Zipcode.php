<?php

namespace App;

use App\Scopes\CachableScope;
use App\{Model, City, State};

class Zipcode extends Model
{
    /**
     * All fillable attributes
     *
     * @var array
     */
	protected $fillable = [
		'id', 'name', 'state_id', 'city_id', 'tax_rate'
	];

	/**
	 * Router will use this column to find the zipcode
	 *
	 * @return string
	 */
	public function getRouteKeyName()
	{
	    return 'name';
	}

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CachableScope);
    }

    /**
     * Get tax rate
     * 
     * @param  string $value
     * @return float       
     */
    public function getTaxRateAttribute($value)
    {
        return config('default-variables.tax_status') ? floatval($value) : 0;
    }

	/**
	 * Zipcode state
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
    public function state()
    {
        return $this->belongsTo(State::class);
    }

    /**
     * Zipcode city
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
