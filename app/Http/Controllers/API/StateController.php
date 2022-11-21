<?php

namespace App\Http\Controllers\API;

use App\State;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CitiesResources;
use App\Http\Resources\StatesResources;

class StateController extends Controller
{
    /**
     * Get all available states
     * 
     * @param  int $countryId
     * @return          
     */
    public function index(int $countryId)
    {
        return StatesResources::collection(
            State::where('country_id', $countryId)->orderBy('name', 'asc')->get()
        );
    }

    /**
     * Show the given state cities
     * 
     * @param  int    $countryId
     * @param  State  $state
     * @return
     */
    public function show(string $country, State $state)
    {
        return CitiesResources::collection($state->cities);
    }
}
