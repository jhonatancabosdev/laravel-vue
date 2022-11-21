<?php

namespace App;

class NofraudTransaction extends Model
{
    protected $guarded = [];

    /**
     * Get order
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
