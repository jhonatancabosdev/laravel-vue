<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1')->group(function () {
    Route::namespace('FME\EloquenceCsvFeed')->group(function () {
        Route::group(['middleware' => ['api']], function () {
            Route::get('fme/{model?}/download/{offset?}/{limit?}', 'EloquanceCsvFeedController@download');
            Route::get('fme/{model?}/generate/{offset?}/{limit?}', 'EloquanceCsvFeedController@getCsv');
        });
    });
});
