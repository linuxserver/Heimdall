<?php

use App\Application;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ItemRestController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
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

if (config('app.url') !== 'http://localhost') {
    URL::forceRootUrl(config('app.url'));
}

Route::get('/userselect/{user}', [LoginController::class, 'setUser'])->name('user.set');
Route::get('/userselect', [UserController::class, 'selectUser'])->name('user.select');
Route::get('/autologin/{uuid}', [LoginController::class, 'autologin'])->name('user.autologin');

Route::get('/', [ItemController::class,'dash'])->name('dash');
Route::get('check_app_list', [ItemController::class,'checkAppList'])->name('applist');

Route::get('single/{appid}', function ($appid) {
    return json_encode(Application::single($appid));
})->name('single');

/**
 * Tag Routes
 */
Route::resource('tags', TagController::class);

Route::name('tags.')->prefix('tag')->group(function () {
    Route::get('/{slug}', [TagController::class, 'show'])->name('show');
    Route::get('/add/{tag}/{item}', [TagController::class, 'add'])->name('add');
    Route::get('/restore/{id}', [TagController::class, 'restore'])->name('restore');
});


/**
 * Item Routes
 */
Route::resource('items', ItemController::class);

Route::name('items.')->prefix('items')->group(function () {
    Route::get('/websitelookup/{url}', [ItemController::class, 'websitelookup'])->name('lookup');
    Route::get('/pin/{id}', [ItemController::class, 'pin'])->name('pin');
    Route::get('/restore/{id}', [ItemController::class, 'restore'])->name('restore');
    Route::get('/unpin/{id}', [ItemController::class, 'unpin'])->name('unpin');
    Route::get('/pintoggle/{id}/{ajax?}/{tag?}', [ItemController::class, 'pinToggle'])->name('pintoggle');
});

Route::post('order', [ItemController::class,'setOrder'])->name('items.order');
Route::post('appload', [ItemController::class,'appload'])->name('appload');
Route::post('test_config', [ItemController::class,'testConfig'])->name('test_config');
Route::get('get_stats/{id}', [ItemController::class,'getStats'])->name('get_stats');

Route::get('/search', [SearchController::class,'index'])->name('search');

Route::get('view/{name_view}', function ($name_view) {
    return view('SupportedApps::'.$name_view)->render();
});

Route::get('titlecolour', function (Request $request) {
    $color = $request->input('color');
    if ($color) {
        return title_color($color);
    }
    return '';
})->name('titlecolour');

Route::resource('users', UserController::class);

/**
 * Settings.
 */
Route::name('settings.')->prefix('settings')->group(function () {
    Route::get('/', [SettingsController::class,'index'])->name('index');
    Route::get('edit/{id}', [SettingsController::class,'edit'])->name('edit');
    Route::get('clear/{id}', [SettingsController::class,'clear'])->name('clear');
    Route::patch('edit/{id}', [SettingsController::class,'update']);
});

Auth::routes(['register' => false]);

Route::get('/home', [HomeController::class,'index'])->name('home');

Route::resource('api/item', ItemRestController::class);
Route::get('import', ImportController::class)->name('items.import');

Route::get('/health', HealthController::class)->name('health');
