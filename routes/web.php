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

Route::get('/', 'ItemController@dash')->name('dash');

Route::resources([
    'items' => 'ItemController',
]);
Route::get('items/pin/{id}', 'ItemController@pin')->name('items.pin');
Route::get('items/restore/{id}', 'ItemController@restore')->name('items.restore');
Route::get('items/unpin/{id}', 'ItemController@unpin')->name('items.unpin');
Route::get('items/pintoggle/{id}/{ajax?}', 'ItemController@pinToggle')->name('items.pintoggle');
Route::post('order', 'ItemController@setOrder')->name('items.order');