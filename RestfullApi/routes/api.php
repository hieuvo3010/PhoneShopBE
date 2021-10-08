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

Route::apiResource('post', 'Api\PostController');
Route::apiResource('category', 'CategoryController');
Route::apiResource('product', 'ProductController');

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {
    Route::post('/login', 'Api\AuthController@login');
    Route::post('/register', 'Api\AuthController@register');
    Route::post('/logout', 'Api\AuthController@logout');
    Route::post('/refresh', 'Api\AuthController@refresh');
    Route::get('/user-profile', 'Api\AuthController@userProfile');
    Route::post('/change-pass',  'Api\AuthController@changePassWord');    
    Route::get('email/verify/{id}', 'Api\VerificationController@verify')->name('verification.verify'); // Make sure to keep this as your route name
    Route::get('email/resend', 'Api\VerificationController@resend')->name('verification.resend'); 

    
// Google Sign In
    Route::post('/get-google-sign-in-url', 'Api\GoogleController@getGoogleSignInUrl');
    Route::get('/callback', 'Api\GoogleController@loginCallback');
});
