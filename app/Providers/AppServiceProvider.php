<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Artisan;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if(!file_exists(database_path(env('DB_DATABASE')))) {
            // first time setup
            touch(database_path(env('DB_DATABASE')));
            Artisan::call('migrate', array('--path' => 'database/migrations', '--force' => true));
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
