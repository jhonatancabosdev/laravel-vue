<?php

use Illuminate\Http\Request;
use App\Shop\Products\Product;
use App\Custom\Zipcode\Zipcode;
use Illuminate\Support\Facades\Route;
use App\Shop\Products\Repositories\ProductRepository;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::namespace('API')->group(function() { 

	Route::get('country','CountryController@index');

	Route::get('country/{country_id}', 'CountryController@show');

	Route::get('country/{country_id}/state', 'StateController@index');

	Route::get('country/{country_id}/state/{state}', 'StateController@show');

	Route::get('country/{country_id}/state/{state}/city', 'CityController@index');

	Route::get('country/{country_id}/state/{state}/city/{city_id}', 'CityController@show');

	Route::get('zipcode/{zipcode}/city', 'ZipcodeController@city');

	Route::get('zipcode/{zipcode}/state', 'ZipcodeController@state');

	Route::post('subscribe', 'SubscribeController@store')->name('subscribe.store');
});

