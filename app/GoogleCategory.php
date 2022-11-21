<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GoogleCategory extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'productCatGoogleMap';


        /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public $timestamps = false;
}
 