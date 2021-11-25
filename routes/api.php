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


Route::group([
    'middleware' => 'api',
    // 'prefix' => 'auth',
   

], function ($router) {
    
    
    Route::apiResource('ratings', 'RatingController')->only(['store']);
    // Route::get('rating', 'RatingController@show');

    Route::apiResource('wishlists', 'WishlistController')->only(['index','store']);
    Route::delete('wishlist', 'WishlistController@delete');

    Route::apiResource('products', 'ProductController')->only(['index','store']);
    Route::get('product', 'ProductController@show');
    Route::put('product', 'ProductController@update');
    Route::delete('products', 'ProductController@delete');

    Route::apiResource('brands', 'BrandController')->only(['index','store']);
    Route::get('brand', 'BrandController@show');
    Route::put('brand', 'BrandController@update');
    Route::delete('brands', 'BrandController@delete');

    Route::get('home/brand/products', 'HomeController@show_product_with_brand');
    Route::get('home/products', 'HomeController@show_product');
    Route::get('home/products_between', 'HomeController@show_product_between_price');
    Route::get('colors-product', 'HomeController@show_color_products');

    Route::group(['prefix' => 'user','middleware' => ['assign.guard:users']],function ()
    {
        // order
        Route::get('/order-detail', 'UserController@show_order_detail');
        Route::get('/order', 'UserController@show_all_order');
        Route::post('/confirm-order', 'CheckoutController@confirm_order');


        // account
        Route::post('/login', 'UserController@login');
        Route::post('/register', 'UserController@register');
        Route::post('/logout', 'UserController@logout');
        Route::post('/refresh', 'UserController@refresh');
        Route::get('/user-profile', 'UserController@userProfile');
        Route::put('/update-profile', 'UserController@updateProfile');
        Route::post('/change-pass',  'UserController@changePassWord');    
        Route::get('email/verify/{id}', 'VerificationController@verify_user')->name('verification.verify'); // Make sure to keep this as your route name
        Route::get('email/resend', 'VerificationController@resend')->name('verification.resend'); 
        
        
    });

    Route::group(['prefix' => 'admin','middleware' => ['assign.guard:admins']],function ()
    {
        // manager order
        Route::delete('/delete-order', 'AdminController@delete_order')->name('delete-order');
        Route::post('/update-order', 'AdminController@update_order')->name('update-order');
        Route::get('/order-user-detail', 'AdminController@show_detail_order');
        Route::get('/order-user', 'AdminController@show_all_order');

        // manager user
        Route::get('/show_account_user', 'AdminController@show_account_user');

        // account
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
