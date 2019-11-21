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

Route::resource('bots', 'BotController')->only('index')->middleware('auth');
Route::resource('users', 'UserController')->middleware('auth');
Route::get('/list/users', 'UserController@getList')->middleware('auth');
Route::resource('webhooks', 'WebhooksController')->only('index')->middleware('auth');
