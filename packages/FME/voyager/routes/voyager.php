<?php

use Illuminate\Support\Str;
use TCG\Voyager\Events\Routing;
use TCG\Voyager\Events\RoutingAdmin;
use TCG\Voyager\Events\RoutingAdminAfter;
use TCG\Voyager\Events\RoutingAfter;
use TCG\Voyager\Facades\Voyager;

/*
|--------------------------------------------------------------------------
| Voyager Routes
|--------------------------------------------------------------------------
|
| This file is where you may override any of the routes that are included
| with Voyager.
|
*/

Route::group(['as' => 'voyager.'], function () {
    event(new Routing());

    $namespacePrefix = '\\'.config('voyager.controllers.namespace').'\\';

    Route::get('login', ['uses' => $namespacePrefix.'VoyagerAuthController@login',     'as' => 'login']);
    Route::post('login', ['uses' => $namespacePrefix.'VoyagerAuthController@postLogin', 'as' => 'postlogin']);

    Route::group(['middleware' => 'admin.user'], function () use ($namespacePrefix) {
        event(new RoutingAdmin());

        // Main Admin and Logout Route
        Route::get('/', ['uses' => $namespacePrefix.'VoyagerController@index',   'as' => 'dashboard']);
        Route::get('/voyager-assets', ['uses' => $namespacePrefix.'VoyagerController@assets', 'as' => 'voyager_assets']);
        Route::post('logout', ['uses' => $namespacePrefix.'VoyagerController@logout',  'as' => 'logout']);
        Route::post('upload', ['uses' => $namespacePrefix.'VoyagerController@upload',  'as' => 'upload']);
        Route::post('clear-cache', ['uses' => $namespacePrefix.'VoyagerController@clearCache',  'as' => 'clear-cache']);

        Route::get('profile', ['uses' => $namespacePrefix.'VoyagerUserController@profile', 'as' => 'profile']);

        try {
            foreach (Voyager::model('DataType')::all() as $dataType) {
                $breadController = $dataType->controller
                                 ? Str::start($dataType->controller, '\\')
                                 : $namespacePrefix.'VoyagerBaseController';

                if (! is_null($dataType->nested_controller)) {
                    try {
                        Route::get($dataType->slug.'/nested', '\\' . get_class(app($dataType->nested_controller)). '@index')
                            ->name($dataType->slug.'.nested.index');

                        Route::get($dataType->slug.'/nested/create', '\\' . get_class(app($dataType->nested_controller)). '@create')
                            ->name($dataType->slug.'.nested.create');

                        Route::get($dataType->slug.'/nested/edit', '\\' . get_class(app($dataType->nested_controller)). '@edit')
                            ->name($dataType->slug.'.nested.edit');

                        Route::put($dataType->slug.'/nested/store', '\\' . get_class(app($dataType->nested_controller)). '@store')
                            ->name($dataType->slug.'.nested.store');

                        Route::put($dataType->slug.'/nested/update', '\\' . get_class(app($dataType->nested_controller)). '@update')
                            ->name($dataType->slug.'.nested.update');

                        Route::put($dataType->slug.'/nested/order', '\\' . get_class(app($dataType->nested_controller)). '@order')
                            ->name($dataType->slug.'.nested.order');

                        Route::delete($dataType->slug.'/nested/destroy', '\\' . get_class(app($dataType->nested_controller)). '@destroy')
                            ->name($dataType->slug.'.nested.destroy');

                        Route::get($dataType->slug.'/nested-item/{id}', '\\' . get_class(app($dataType->nested_controller)). '@show')
                            ->name($dataType->slug.'.nested.show');
                    } catch (\Exception $e) {
                    }
                }

                Route::post($dataType->slug.'/{id}/{column}/toggle-column', $breadController.'@toggleColumn')->name($dataType->slug.'.toggle-column');
                Route::get($dataType->slug.'/order', $breadController.'@order')->name($dataType->slug.'.order');
                Route::post($dataType->slug.'/action', $breadController.'@action')->name($dataType->slug.'.action');
                Route::post($dataType->slug.'/order', $breadController.'@update_order')->name($dataType->slug.'.update_order');
                ;
                Route::get($dataType->slug.'/{id}/restore', $breadController.'@restore')->name($dataType->slug.'.restore');
                Route::get($dataType->slug.'/relation', $breadController.'@relation')->name($dataType->slug.'.relation');
                Route::post($dataType->slug.'/remove', $breadController.'@remove_media')->name($dataType->slug.'.media.remove');
                Route::resource($dataType->slug, $breadController, ['parameters' => [$dataType->slug => 'id']]);
            }

            Route::get('export-products', $namespacePrefix . 'Bread\\ProductBreadController@export')->name('products.export');
            Route::post('products/{any_product}/brands', $namespacePrefix . 'Bread\\BrandBreadController@storeAndAttach')->name('brands.store.attach');
            Route::post('products/categories', $namespacePrefix . 'Bread\\ProductBreadController@updateCategories')->name('products.categories');
            Route::post('products/{any_product?}/destroy-image', $namespacePrefix . 'Bread\\ProductBreadController@destroyImage')->name('products.destroy.image');
            Route::post('products/{any_product?}/options', $namespacePrefix . 'Bread\\ProductBreadController@updateProductOptions')->name('products.update.options');
            Route::delete('products/{any_product?}/destroy', $namespacePrefix . 'Bread\\ProductBreadController@destroyProductOptions')->name('products.destroy.options');

            Route::post('/order/{order}/refund', $namespacePrefix . 'Bread\\OrderBreadController@refund')->name('refund.order');
            Route::post('/order/{order}/{column}', $namespacePrefix . 'Bread\\OrderBreadController@updateColumn')->name('orders.update-column');
            Route::post('/order/notify', $namespacePrefix . 'Bread\\OrderBreadController@notify')->name('mail.order');
            Route::get('/order/export', $namespacePrefix . 'Bread\\OrderBreadController@export')->name('export.order');

            Route::post('/tracking/create', $namespacePrefix . 'Bread\\TrackingNumbersController@create')->name('create.tracking');
        } catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException("Custom routes hasn't been configured because: ".$e->getMessage(), 1);
        } catch (\Exception $e) {
            // do nothing, might just be because table not yet migrated.
        }

        // Menu Routes
        Route::group([
            'as'     => 'menus.',
            'prefix' => 'menus/{menu}',
        ], function () use ($namespacePrefix) {
            Route::get('builder', ['uses' => $namespacePrefix.'VoyagerMenuController@builder',    'as' => 'builder']);
            Route::post('order', ['uses' => $namespacePrefix.'VoyagerMenuController@order_item', 'as' => 'order_item']);

            Route::group([
                'as'     => 'item.',
                'prefix' => 'item',
            ], function () use ($namespacePrefix) {
                Route::delete('{id}', ['uses' => $namespacePrefix.'VoyagerMenuController@delete_menu', 'as' => 'destroy']);
                Route::post('/', ['uses' => $namespacePrefix.'VoyagerMenuController@add_item',    'as' => 'add']);
                Route::put('/', ['uses' => $namespacePrefix.'VoyagerMenuController@update_item', 'as' => 'update']);
            });
        });

        // Settings
        Route::group([
            'as'     => 'settings.',
            'prefix' => 'settings',
        ], function () use ($namespacePrefix) {
            Route::get('/', ['uses' => $namespacePrefix.'VoyagerSettingsController@index',        'as' => 'index']);
            Route::post('/', ['uses' => $namespacePrefix.'VoyagerSettingsController@store',        'as' => 'store']);
            Route::put('/', ['uses' => $namespacePrefix.'VoyagerSettingsController@update',       'as' => 'update']);
            Route::delete('{id}', ['uses' => $namespacePrefix.'VoyagerSettingsController@delete',       'as' => 'delete']);
            Route::get('{id}/move_up', ['uses' => $namespacePrefix.'VoyagerSettingsController@move_up',      'as' => 'move_up']);
            Route::get('{id}/move_down', ['uses' => $namespacePrefix.'VoyagerSettingsController@move_down',    'as' => 'move_down']);
            Route::put('{id}/delete_value', ['uses' => $namespacePrefix.'VoyagerSettingsController@delete_value', 'as' => 'delete_value']);
        });

        // Admin Media
        Route::group([
            'as'     => 'media.',
            'prefix' => 'media',
        ], function () use ($namespacePrefix) {
            Route::get('/', ['uses' => $namespacePrefix.'VoyagerMediaController@index',              'as' => 'index']);
            Route::post('files', ['uses' => $namespacePrefix.'VoyagerMediaController@files',              'as' => 'files']);
            Route::post('new_folder', ['uses' => $namespacePrefix.'VoyagerMediaController@new_folder',         'as' => 'new_folder']);
            Route::post('delete_file_folder', ['uses' => $namespacePrefix.'VoyagerMediaController@delete', 'as' => 'delete']);
            Route::post('move_file', ['uses' => $namespacePrefix.'VoyagerMediaController@move',          'as' => 'move']);
            Route::post('rename_file', ['uses' => $namespacePrefix.'VoyagerMediaController@rename',        'as' => 'rename']);
            Route::post('upload', ['uses' => $namespacePrefix.'VoyagerMediaController@upload',             'as' => 'upload']);
            Route::post('crop', ['uses' => $namespacePrefix.'VoyagerMediaController@crop',             'as' => 'crop']);
        });

        // BREAD Routes
        Route::group([
            'as'     => 'bread.',
            'prefix' => 'bread',
        ], function () use ($namespacePrefix) {
            Route::get('/', ['uses' => $namespacePrefix.'VoyagerBreadController@index',              'as' => 'index']);
            Route::get('{table}/create', ['uses' => $namespacePrefix.'VoyagerBreadController@create',     'as' => 'create']);
            Route::post('/', ['uses' => $namespacePrefix.'VoyagerBreadController@store',   'as' => 'store']);
            Route::get('{table}/edit', ['uses' => $namespacePrefix.'VoyagerBreadController@edit', 'as' => 'edit']);
            Route::put('{id}', ['uses' => $namespacePrefix.'VoyagerBreadController@update',  'as' => 'update']);
            Route::delete('{id}', ['uses' => $namespacePrefix.'VoyagerBreadController@destroy',  'as' => 'delete']);
            Route::post('relationship', ['uses' => $namespacePrefix.'VoyagerBreadController@addRelationship',  'as' => 'relationship']);
            Route::get('delete_relationship/{id}', ['uses' => $namespacePrefix.'VoyagerBreadController@deleteRelationship',  'as' => 'delete_relationship']);

            Route::post('/products/ajax-update', ['uses' => $namespacePrefix.'Bread\\ProductBreadController@ajaxUpdate', 'as' => 'ajax.update']);
        });

        // Database Routes
        Route::resource('database', $namespacePrefix.'VoyagerDatabaseController');
        
        event(new RoutingAdminAfter());
    });

    event(new RoutingAfter());
});
