<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Artisan;
use App\Setting;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $alt_bg = '';

        if(!is_file(database_path(env('DB_DATABASE')))) {
            // first time setup
            touch(database_path(env('DB_DATABASE')));
            Artisan::call('migrate', array('--path' => 'database/migrations', '--force' => true, '--seed' => true));
            Artisan::call('storage:link');
            //Cache
            //Artisan::call('config:cache');
            //Artisan::call('route:cache');
        }
        if(is_file(database_path(env('DB_DATABASE')))) {
            if($bg_image = Setting::fetch('background_image')) {
                $alt_bg = ' style="background-image: url('.asset('storage/'.$bg_image).')"';
            }

            // check version to see if an upgrade is needed
            $db_version = Setting::fetch('version');
            $app_version = config('app.version');
            if(version_compare($app_version, $db_version) == 1) { // app is higher than db, so need to run migrations etc
                Artisan::call('migrate', array('--path' => 'database/migrations', '--force' => true, '--seed' => true));                   
            }
        }
        view()->share('alt_bg', $alt_bg);

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('settings', function () {
            return new Setting();
        });
    }
}
