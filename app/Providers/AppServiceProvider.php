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
            Artisan::call('storage:link');
            //Cache
            //Artisan::call('config:cache');
            //Artisan::call('route:cache');
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
