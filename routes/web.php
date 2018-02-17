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
    'tags' => 'TagController',
]);

Route::get('tag/{slug}', 'TagController@show')->name('tags.show');
Route::get('tag/add/{tag}/{item}', 'TagController@add')->name('tags.add');


Route::get('items/pin/{id}', 'ItemController@pin')->name('items.pin');
Route::get('items/restore/{id}', 'ItemController@restore')->name('items.restore');
Route::get('items/unpin/{id}', 'ItemController@unpin')->name('items.unpin');
Route::get('items/pintoggle/{id}/{ajax?}', 'ItemController@pinToggle')->name('items.pintoggle');
Route::post('order', 'ItemController@setOrder')->name('items.order');

Route::post('appload', 'ItemController@appload')->name('appload');
Route::post('test_config', 'ItemController@testConfig')->name('test_config');
Route::get('/get_stats/{id}', 'ItemController@getStats')->name('get_stats');

Route::get('view/{name_view}', function ($name_view) {
    return view('supportedapps.'.$name_view);
});

/**
 * Settings.
 */
Route::group([
    'as'     => 'settings.',
    'prefix' => 'settings',
], function () {

    Route::get('/', 'SettingsController@index')
        ->name('index');
    Route::get('edit/{id}', 'SettingsController@edit')
        ->name('edit');
    Route::get('clear/{id}', 'SettingsController@clear')
        ->name('clear');


    Route::patch('edit/{id}', 'SettingsController@update');

});