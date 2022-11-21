<?php

namespace App\Http\Controllers\API;

use App\City;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CityController extends Controller
{
    public function index($countryId, $stateId)
    {
        return response()->json([
            'data' => City::where('state_id', $stateId)->orderBy('name', 'asc')->get()
        ]);
    }

    public function show($countryId, $stateId, City $city)
    {
        return response()->json([
            'data' => $city
        ]);
    }
}
