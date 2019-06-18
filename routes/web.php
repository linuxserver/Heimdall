<?php

use Illuminate\Http\Request;

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

if(\Config::get('app.url') !== 'http://localhost') {
    URL::forceRootUrl(\Config::get('app.url'));
}

Route::get('/userselect/{user}', 'Auth\LoginController@setUser')->name('user.set');
Route::get('/userselect', 'UserController@selectUser')->name('user.select');
Route::get('/autologin/{uuid}', 'Auth\LoginController@autologin')->name('user.autologin');

Route::get('/', 'ItemController@dash')->name('dash');
Route::get('check_app_list', 'ItemController@checkAppList')->name('applist');

Route::resources([
    'items' => 'ItemController',
    'tags' => 'TagController',
]);



Route::get('tag/{slug}', 'TagController@show')->name('tags.show');
Route::get('tag/add/{tag}/{item}', 'TagController@add')->name('tags.add');
Route::get('tag/restore/{id}', 'TagController@restore')->name('tags.restore');


Route::get('items/pin/{id}', 'ItemController@pin')->name('items.pin');
Route::get('items/restore/{id}', 'ItemController@restore')->name('items.restore');
Route::get('items/unpin/{id}', 'ItemController@unpin')->name('items.unpin');
Route::get('items/pintoggle/{id}/{ajax?}/{tag?}', 'ItemController@pinToggle')->name('items.pintoggle');
Route::post('order', 'ItemController@setOrder')->name('items.order');

Route::post('appload', 'ItemController@appload')->name('appload');
Route::post('test_config', 'ItemController@testConfig')->name('test_config');
Route::get('/get_stats/{id}', 'ItemController@getStats')->name('get_stats');

Route::get('/search', 'SearchController@index')->name('search');

Route::get('view/{name_view}', function ($name_view) {
    return view('SupportedApps::'.$name_view)->render();
});

Route::get('titlecolour', function (Request $request) {
    $color = $request->input('color');
    if($color) {
        return title_color($color);
    };
    
})->name('titlecolour');   

Route::resource('users', 'UserController');

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
Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
