<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class TrackingNumber extends Model
{
    use Notifiable;

	/**
	 * @var array
	 */
    protected $fillable = [
    	'user_file_id',
        'lot_number',
    	'quantity',
    	'order_id',
    	'number',
    	'name',
    ];

    /**
     * @return BelongsTo
     */
    public function order()
    {
    	return $this->belongsTo(Order::class);
    }
  
    /**
     * @return BelongsTo
     */
    public function userFile()
    {
        return $this->belongsTo(UserFile::class);
    }
}
