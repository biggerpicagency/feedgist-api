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

    Route::middleware('jwt')->group(function () {
        Route::get('feed/list', 'FeedController@feedList');
        Route::get('feed/settings',  'FeedController@settings');
        Route::put('feed/settings', 'FeedController@saveSettings');
    
        Route::get('settings/{playerId}', 'SettingsController@get');
        Route::delete('settings/{playerId}', 'SettingsController@remove');
        Route::put('settings', 'SettingsController@add');
        Route::post('sendMessage', 'SettingsController@sendMessage');
    });
});