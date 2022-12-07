<?php

use Illuminate\Support\Facades\Route;

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
    return \Illuminate\Support\Facades\App::version();
});

Route::group(['prefix' => 'check-availability'], function () {
    Route::get('/?{dateStart}&{dateEnd}', 'AvailabilityController@check');
    Route::get('/', 'AvailabilityController@index');
    Route::post('/', 'AvailabilityController@book');
    Route::put('/', 'AvailabilityController@update');
    Route::delete('/', 'AvailabilityController@delete');
});

