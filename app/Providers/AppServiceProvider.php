<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
            //die("No Database");
            touch(database_path(env('DB_DATABASE')));
            \Artisan::call('migrate', array('--path' => 'app/migrations', '--force' => true));
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
