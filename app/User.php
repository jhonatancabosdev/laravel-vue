<?php

namespace App;

use TCG\Voyager\Models\Role;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Models\User as VoyagerUser;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends VoyagerUser
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'id'
    ];

    /**
     * All carbon dates
     * 
     * @var array 
     */
    protected $dates = [
        'last_login', 'created_at', 'updated_at', 'deleted_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
}
