<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::group(['prefix' => 'auth'], function () {
    Route::options('/register', 'Auth\RegisterController@options');
    Route::post('/register', 'Auth\RegisterController@store');

    Route::options('/login', 'Auth\LoginController@options');
    Route::post('/login', 'Auth\LoginController@store');
});

Route::group(['prefix' => 'password'], function() {
    Route::options('/forget', 'Auth\PasswordForgetController@options');
    Route::post('/forget', 'Auth\PasswordForgetController@store');
});