<?php

use Illuminate\Http\Request;

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

// Route::apiResource('post', 'Api\PostController');
// Route::apiResource('category', 'CategoryController');
// Route::apiResource('product', 'ProductController');
// Route::apiResource('brand', 'BrandController');

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {
    
   
    Route::apiResource('product', 'ProductController');
    Route::apiResource('brand', 'BrandController');
    Route::get('home/brands/{id}', 'HomeController@show_product_with_brand');
    Route::get('home/product/new', 'HomeController@show_product_new');
    Route::get('home/product/{sort}', 'HomeController@show_product');
    
    Route::group(['prefix' => 'user','middleware' => ['assign.guard:users']],function ()
    {
        Route::post('/login', 'Api\UserController@login');
        Route::post('/register', 'Api\UserController@register');
        Route::post('/logout', 'Api\UserController@logout');
        Route::post('/refresh', 'Api\UserController@refresh');
        Route::get('/user-profile', 'Api\UserController@userProfile');
        Route::post('/change-pass',  'Api\UserController@changePassWord');    
        Route::get('email/verify/{id}', 'Api\VerificationController@verify_user')->name('verification.verify'); // Make sure to keep this as your route name
        Route::get('email/resend', 'Api\VerificationController@resend')->name('verification.resend'); 
        Route::post('/confirm-order', 'CheckoutController@confirm_order');
        
    });

    Route::group(['prefix' => 'admin','middleware' => ['assign.guard:admins']],function ()
    {
        Route::post('/login', 'Api\AdminController@login');
        Route::post('/register', 'Api\AdminController@register');
        Route::post('/logout', 'Api\AdminController@logout');
        Route::post('/refresh', 'Api\AdminController@refresh');
        Route::get('/user-profile', 'Api\AdminController@userProfile');
        Route::post('/change-pass',  'Api\AdminController@changePassWord');    
        Route::get('email/verify/{id}', 'Api\VerificationController@verify_admin')->name('verification.verify_admin'); // Make sure to keep this as your route name
        Route::get('email/resend', 'Api\VerificationController@resend')->name('verification.resend'); 
    });
    
// Google Sign In
    Route::post('/get-google-sign-in-url', 'Api\GoogleController@getGoogleSignInUrl');
    Route::get('/callback', 'Api\GoogleController@loginCallback');
});
