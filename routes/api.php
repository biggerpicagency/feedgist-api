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

    Route::get('feed/list', ['middleware' => ['jwt'], 'uses' => 'FeedController@feedList']);
    Route::get('feed/settings', ['middleware' => ['jwt'], 'uses' => 'FeedController@settings']);
    Route::put('feed/settings', ['middleware' => ['jwt'], 'uses' => 'FeedController@saveSettings']);

    Route::get('settings/{playerId}', ['middleware' => ['jwt'], 'uses' => 'SettingsController@get']);
    Route::delete('settings/{playerId}', ['middleware' => ['jwt'], 'uses' => 'SettingsController@remove']);
    Route::put('settings', ['middleware' => ['jwt'], 'uses' => 'SettingsController@add']);
});