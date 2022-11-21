<?php

namespace App;

use App\Scopes\CachableScope;
use App\{Model, State, Zipcode};

class City extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'name', 'timezone', 'state_id', 'country_id',
    ];

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
     * Related state
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function state()
    {
        return $this->belongsTo(State::class)
                    ->remember(self::getDefaultCacheTime());
    }

    /**
     * Related zipcodes
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function zipcodes()
    {
        return $this->hasMany(Zipcode::class)
                    ->remember(self::getDefaultCacheTime());
    }
}
