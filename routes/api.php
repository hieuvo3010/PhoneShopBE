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
    // 'prefix' => 'auth',
   

], function ($router) {
    
   
    Route::apiResource('product', 'ProductController')->except([
        'show','update','destroy'
    ]);
    Route::get('product_s', 'ProductController@show');
    Route::put('product', 'ProductController@update');
    Route::delete('product', 'ProductController@delete');

    Route::apiResource('brand', 'BrandController')->except([
        'show','destroy',
    ]);

    Route::get('brand_s', 'BrandController@show');
    Route::put('brand', 'BrandController@update');
    Route::delete('brand', 'BrandController@delete');

    Route::get('home/brand/products', 'HomeController@show_product_with_brand');
    Route::get('home/products', 'HomeController@show_product');
    Route::get('home/products_between', 'HomeController@show_product_between_price');
    
    Route::group(['prefix' => 'user','middleware' => ['assign.guard:users']],function ()
    {
        Route::get('/order-detail', 'Api\UserController@show_order_detail');
        Route::get('/order', 'Api\UserController@show_all_order');
        Route::post('/login', 'Api\UserController@login');
        Route::post('/register', 'Api\UserController@register');
        Route::post('/logout', 'Api\UserController@logout');
        Route::post('/refresh', 'Api\UserController@refresh');
        Route::get('/user-profile', 'Api\UserController@userProfile');
        Route::put('/update-profile', 'Api\UserController@updateProfile');
        Route::post('/change-pass',  'Api\UserController@changePassWord');    
        Route::get('email/verify/{id}', 'Api\VerificationController@verify_user')->name('verification.verify'); // Make sure to keep this as your route name
        Route::get('email/resend', 'Api\VerificationController@resend')->name('verification.resend'); 
        Route::post('/confirm-order', 'CheckoutController@confirm_order');
        
    });

    Route::group(['prefix' => 'admin','middleware' => ['assign.guard:admins']],function ()
    {
        
        Route::get('/order-user-detail', 'Api\AdminController@show_detail_order');
        Route::get('/order-user', 'Api\AdminController@show_all_order');
        Route::get('/show_account_user', 'Api\AdminController@show_account_user');
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
