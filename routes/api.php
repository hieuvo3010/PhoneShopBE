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

], function ($router) {

    
    // Search 
    Route::get('/search', 'HomeController@getSearchResults')->name('search');
    
    // Google Sign In
    Route::post('/get-google-sign-in-url', 'GoogleController@getGoogleSignInUrl');
    Route::get('/google/callback', 'GoogleController@loginCallback');

    // facebook Sign In
    Route::post('/get-facebook-sign-in-url', 'FacebookController@getFacebookSignInUrl');
    Route::get('/facebook/callback', 'FacebookController@loginCallback');
    
    // article
    Route::apiResource('articles', 'ArticleController')->only(['index','store']);
    Route::get('article', 'ArticleController@show');
    Route::put('article', 'ArticleController@update');
    Route::delete('article', 'ArticleController@delete');

    // category-article
    Route::apiResource('cate-articles', 'CateArticleController')->only(['index','store']);
    Route::get('cate-article', 'CateArticleController@show');
    Route::put('cate-article', 'CateArticleController@update');
    Route::delete('cate-article', 'CateArticleController@delete');
    
    // rating
    Route::apiResource('ratings', 'RatingController')->only(['store']);
    Route::get('rating', 'RatingController@show');

    // wishlist
    Route::apiResource('wishlists', 'WishlistController')->only(['index','store']);
    Route::delete('wishlist', 'WishlistController@delete_product_on_wishlist');

    // product
    Route::apiResource('products', 'ProductController')->only(['index','store']);
    Route::get('product', 'ProductController@show');
    Route::put('product', 'ProductController@update');
    Route::delete('product', 'ProductController@delete');
    Route::get('related-products', 'ProductController@related_products');
    Route::get('type-products', 'ProductController@type_product');

    // category
    Route::apiResource('categories', 'CategoryController')->only(['index','store']);
    Route::get('category', 'CategoryController@show');
    Route::put('category', 'CategoryController@update');
    Route::delete('category', 'CategoryController@delete');

    // coupon
    Route::apiResource('coupons', 'CouponController')->only(['index','store']);
    Route::get('coupon', 'CouponController@show');
    Route::put('coupon', 'CouponController@update');
    Route::delete('coupon', 'CouponController@delete');

    // brand
    Route::apiResource('brands', 'BrandController')->only(['index','store']);
    Route::get('brand', 'BrandController@show');
    Route::put('brand', 'BrandController@update');
    Route::delete('brand', 'BrandController@delete');

    // show public
    Route::get('products/filter', 'HomeController@show_product');
    Route::get('colors-product', 'HomeController@show_color_products');
    Route::get('cate/articles', 'HomeController@get_articles_by_cate');
    Route::get('tra-cuu-don-hang', 'HomeController@check_order');

    Route::group(['prefix' => 'user','middleware' => ['assign.guard:users']],function ()
    {
        // order
        Route::get('/order-detail', 'UserController@show_order_detail');
        Route::get('/order', 'UserController@show_all_order');
        Route::post('/confirm-order', 'CheckoutController@confirm_order');
        Route::delete('/delete-order', 'UserController@delete_order');

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

        Route::post('reset-password', 'ResetPasswordController@sendMail');
        Route::put('reset-password', 'ResetPasswordController@reset');

    });

    Route::group(['prefix' => 'admin','middleware' => ['assign.guard:admins']],function ()
    {
        // show 
        Route::get('/{type}/all', 'AdminController@show');

        // search brand, category and
        Route::get('/{type}/search', 'AdminController@search');

        // manager order
        Route::delete('/delete-order', 'AdminController@delete_order')->name('delete-order');
        Route::post('/update-order', 'AdminController@update_order')->name('update-order');
        Route::get('/order-user-detail', 'AdminController@show_detail_order');
        Route::get('/order-user', 'AdminController@show_all_order');
        Route::put('/update-user', 'AdminController@update_user');

        Route::get('/dashboard', 'AdminController@dashboard');


        // account
        Route::post('/login', 'AdminController@login');
        Route::post('/register', 'AdminController@register');
        Route::post('/logout', 'AdminController@logout');
        Route::post('/refresh', 'AdminController@refresh');
        Route::get('/user-profile', 'AdminController@userProfile');
        Route::post('/change-pass',  'AdminController@changePassWord');    
        // Route::get('email/verify/{id}', 'VerificationController@verify_admin')->name('verification.verify_admin'); // Make sure to keep this as your route name
        // Route::get('email/resend', 'VerificationController@resend')->name('verification.resend');

    });
    

});
