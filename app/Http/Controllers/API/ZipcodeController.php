<?php

namespace App\Http\Controllers\API;

use App\Zipcode;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ZipcodeController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  Zipcode $zipcode
     * @return \Illuminate\Http\Response
     */
    public function state(Zipcode $zipcode)
    {
        return  $zipcode->state()
                    ->orderBy('name', 'asc')
                    ->first()
                    ->only(['id', 'name', 'status']);
    }

    /**
     * Display the specified resource.
     *
     * @param  Zipcode $zipcode
     * @return \Illuminate\Http\Response
     */
    public function city(Zipcode $zipcode)
    {
        return  $zipcode->city->only(['name', 'id', 'status']);
    }
}
