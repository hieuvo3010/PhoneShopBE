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

// Route::apiResource('post', 'PostController');
// Route::apiResource('category', 'CategoryController');
// Route::apiResource('product', 'ProductController');
// Route::apiResource('brand', 'BrandController');

Route::group([
    'middleware' => 'api',
    // 'prefix' => 'auth',
   

], function ($router) {
    
    Route::apiResource('rating', 'RatingController')->except([
        'show','update','destroy'
    ]);
    Route::get('rating_s', 'RatingController@show');
    Route::apiResource('wishlist', 'WishlistController')->except([
        'show','update','destroy'
    ]);
    Route::delete('wishlist', 'WishlistController@delete');

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
        Route::get('/order-detail', 'UserController@show_order_detail');
        Route::get('/order', 'UserController@show_all_order');
        Route::post('/login', 'UserController@login');
        Route::post('/register', 'UserController@register');
        Route::post('/logout', 'UserController@logout');
        Route::post('/refresh', 'UserController@refresh');
        Route::get('/user-profile', 'UserController@userProfile');
        Route::put('/update-profile', 'UserController@updateProfile');
        Route::post('/change-pass',  'UserController@changePassWord');    
        Route::get('email/verify/{id}', 'VerificationController@verify_user')->name('verification.verify'); // Make sure to keep this as your route name
        Route::get('email/resend', 'VerificationController@resend')->name('verification.resend'); 
        Route::post('/confirm-order', 'CheckoutController@confirm_order');
        
    });

    Route::group(['prefix' => 'admin','middleware' => ['assign.guard:admins']],function ()
    {
        
        Route::get('/order-user-detail', 'AdminController@show_detail_order');
        Route::get('/order-user', 'AdminController@show_all_order');
        Route::get('/show_account_user', 'AdminController@show_account_user');
        Route::post('/login', 'AdminController@login');
        Route::post('/register', 'AdminController@register');
        Route::post('/logout', 'AdminController@logout');
        Route::post('/refresh', 'AdminController@refresh');
        Route::get('/user-profile', 'AdminController@userProfile');
        
        Route::post('/change-pass',  'AdminController@changePassWord');    
        Route::get('email/verify/{id}', 'VerificationController@verify_admin')->name('verification.verify_admin'); // Make sure to keep this as your route name
        Route::get('email/resend', 'VerificationController@resend')->name('verification.resend');

    });
    
// Google Sign In
    Route::post('/get-google-sign-in-url', 'GoogleController@getGoogleSignInUrl');
    Route::get('/callback', 'GoogleController@loginCallback');
});
