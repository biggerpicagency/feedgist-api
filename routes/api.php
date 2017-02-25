<?php

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

Route::group(['middleware' => 'api'], function () {
    Route::post('auth/facebookCallback', 'SocialAuthController@callback');
    Route::get('feed/settings', ['middleware' => ['jwt'], 'uses' => 'FeedController@settings']);
});