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

Route::get('/', function () {
    return view('home');
});

Auth::routes([
    'register' => false,
    'verify' => false,
]);

Route::namespace('Auth')->group(function () {
    Route::get('/redirect', 'SocialAuthGoogleController@redirect');
    Route::get('/callback', 'SocialAuthGoogleController@callback');
});

Route::group(['middleware' => ['auth']], function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    });
    Route::get('/list/users', 'UserController@getList');
    Route::group(['prefix' => 'admin', 'middleware' => ['admin']], function () {
        Route::resource('users', 'UserController');
        Route::group(['namespace' => 'Admin'], function () {
            Route::get('webhooks', 'WebhookController@index')->name('admin.webhooks.index');
        });
    });
    Route::resource('bots', 'BotController')->except('show');
    Route::put('webhooks/change_status', 'WebhookController@changeStatus');
    Route::resource('webhooks', 'WebhookController');
    Route::resource('rooms', 'RoomController')->only([
        'index'
    ]);
    Route::resource('webhooks.payloads', 'PayloadController')->except('show');
});
