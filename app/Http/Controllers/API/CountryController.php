<?php

namespace App\Http\Controllers\API;

use App\Country;
use App\Http\Controllers\Controller;

class CountryController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => Country::get(['id', 'name'])
        ]);
    }

    public function show($id)
    {
        return response()->json([
            'data' => Country::whereId($id)->get(['id', 'name'])
        ]);
    }
}
