<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['urlPreTraitement']], function() {

    /**
     * Backend routes
     */
    Route::group(['prefix' => 'fme-admin'], function () {
        Voyager::routes();
    });

    /**
     * Frontend routes
     */

    Route::namespace('Customer')->group(function() {
        Auth::routes();
        Route::post('/register', 'Auth\RegisterController@register')->middleware('verifyReCaptcha');
    });

    // Sitemap
    Route::get('/sitemap', 'SitemapController@index')->name('sitemap');

    Route::group(['middleware' => ['auth:customer']], function () {

        // customer routes
        Route::namespace('Customer')->group(function() {
            Route::get('account', 'AccountController@index')->name('customer.account');
            Route::resource('/customer.address', 'CustomerAddressController')->only(['index', 'create', 'store', 'destroy']);
            Route::any('customer/address', 'CustomerAddressController@update')->name('customer.address.update');
        });

        Route::get('checkout', 'CheckoutController@index')->name('checkout.index');
        Route::post('checkout', 'CheckoutController@store')->name('checkout.store');
    });

    Route::group(['middleware' => ['checkoutValidation']], function () {
        Route::get('shipping', 'ShippingController@index')->name('shipping.index');
        Route::put('shipping', 'ShippingController@update')->name('shipping.update');
        Route::get('confirm', 'CheckoutBaseController@confirm')->name('checkout.confirm');
        Route::post('execute', 'CheckoutBaseController@execute')->name('checkout.execute');
        Route::get('success', 'CheckoutBaseController@success')->name('checkout.success');
        Route::get('invoice/{order?}', 'InvoiceController@show')->name('invoice.show');
    });

    Route::post('/shipping', 'ShippingController@store')->name('shipping.store');

    // Sitemap
    Route::get('/sitemap', 'SitemapController@index')->name('sitemap');

    Route::group(['middleware' => []], function () {

        Route::get('/', 'HomeController@index')->name('home');
        Route::get('company-information', 'HomeController@companyInformation')->name('company-information');
        Route::get('faq', 'HomeController@faq')->name('faq');
        Route::get('vision', 'HomeController@vision')->name('vision');
        Route::get('privacy-policy', 'HomeController@privacyPolicy')->name('privacy-policy');
        Route::get('terms-and-conditions', 'HomeController@terms')->name('terms-and-conditions');
        Route::get('returns', 'HomeController@returns')->name('returns');
        Route::get('refunds', 'HomeController@refunds')->name('refunds');
        Route::get('about-us', 'HomeController@aboutUs')->name('about-us');
        Route::get('contact-us', 'HomeController@contactUs')->name('contact-us');
        Route::get('shipping-policy', 'HomeController@shippingPolicy')->name('shipping-policy');
        Route::post('/contact-us', 'ContactController@contact')->middleware('verifyReCaptcha')->name('contact');

        Route::get('/favorites', 'FavoritesController@show')->name('favorites');
        Route::get('/recently-viewed-products', 'RecentProductsController@show')->name('recents');

        Route::group(['middleware' => ['guest.cart:customer']], function(){
            Route::get('guest-checkout', 'GuestCheckoutController@index')->name('guest.checkout.index');
        });
        
        Route::post('guest-checkout', 'GuestCheckoutController@store')->name('guest.checkout.store');
        
        Route::resource('cart', 'CartController');
        Route::post('address-validation', 'AddressValidationController@store')->name('address-validation.store');
        Route::get('cart/couponcode/show', 'CartController@getCouponCode')->name('getCouponCode');
        Route::get('cart/couponcode/{couponcode}', 'CartController@applyCouponCode')->name('applyCouponCode');

        Route::put('shipping', 'ShippingController@update')->name('shipping.update');
        Route::put('tax/{zipcode?}', 'TaxController@update')->name('tax.update');

        Route::resource('category', 'CategoryController')->only(['index', 'show']);
        Route::get('category/filter/{id}', 'CategoryController@filterCategory')->name('category.filter');

        Route::resource('brand', 'BrandController')->only(['index', 'show']);

        // product controller
        Route::get("brand-category-search", 'ProductController@categoryBrandSearch')->name('product.brandCatSearch');
        Route::get("search", 'ProductController@search')->name('product.search');
        Route::get('/product/{product?}', 'ProductController@show')->name('product.show');
        Route::get('related-products/{product}', 'ProductController@relatedProducts')->name('product.related');
        Route::get('quick-view/{product}', 'ProductController@quickView')->name('product.quickview');
        Route::post('review/{product}', 'ProductController@review')->name('product.review');
    });

    Route::fallback('HomeController@fallback');

});
