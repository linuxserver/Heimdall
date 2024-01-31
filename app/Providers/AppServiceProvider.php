<?php

namespace App\Providers;

use App\Application;
use App\Jobs\ProcessApps;
use App\Jobs\UpdateApps;
use App\Setting;
use App\User;
use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (! class_exists('ZipArchive')) {
            die('You are missing php-zip');
        }

        $this->createEnvFile();

        $this->setupDatabase();

        if (! is_file(public_path('storage/.gitignore'))) {
            Artisan::call('storage:link');
            \Session::put('current_user', null);
        }

        $applications = Application::all();

        if ($applications->count() <= 0) {
            ProcessApps::dispatch();
        }

        $lang = Setting::fetch('language');
        \App::setLocale($lang);

        // User specific settings need to go here as session isn't available at this point in the app
        view()->composer('*', function ($view) {
            if (isset($_SERVER['HTTP_AUTHORIZATION']) && ! empty($_SERVER['HTTP_AUTHORIZATION'])) {
                list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) =
                explode(':', base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));
            }
            if (! \Auth::check()) {
                if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])
                        && ! empty($_SERVER['PHP_AUTH_USER']) && ! empty($_SERVER['PHP_AUTH_PW'])) {
                    $credentials = ['username' => $_SERVER['PHP_AUTH_USER'], 'password' => $_SERVER['PHP_AUTH_PW']];

                    if (\Auth::attempt($credentials, true)) {
                        // Authentication passed...
                        $user = \Auth::user();
                        //\Session::put('current_user', $user);
                        session(['current_user' => $user]);
                    }
                } elseif (isset($_SERVER['REMOTE_USER']) && ! empty($_SERVER['REMOTE_USER'])) {
                    $user = User::where('username', $_SERVER['REMOTE_USER'])->first();
                    if ($user) {
                        \Auth::login($user, true);
                        session(['current_user' => $user]);
                    }
                }
            }

            $alt_bg = '';
            $trianglify = 'false';
            $trianglify_seed = null;
            if (Setting::fetch('trianglify')) {
                $trianglify = 'true';
                $trianglify_seed = Setting::fetch('trianglify_seed');
            } elseif ($bg_image = Setting::fetch('background_image')) {
                $alt_bg = ' style="background-image: url(storage/'.$bg_image.')"';
            }

            $allusers = User::all();
            $current_user = User::currentUser();

            $view->with('alt_bg', $alt_bg);
            $view->with('trianglify', $trianglify);
            $view->with('trianglify_seed', $trianglify_seed);
            $view->with('allusers', $allusers);
            $view->with('current_user', $current_user);
            if (config('app.auth_roles_enable')){
                $view->with('enable_auth_admin_controles', in_array(config('app.auth_roles_admin'),explode(config('app.auth_roles_delimiter'), $_SERVER[config('app.auth_roles_http_header')])));
            } else {
                $view->with('enable_auth_admin_controles', true);
            }
        });

        $this->app['view']->addNamespace('SupportedApps', app_path('SupportedApps'));

        if (env('FORCE_HTTPS') === true) {
            \URL::forceScheme('https');
        }

        if (env('APP_URL') != 'http://localhost') {
            \URL::forceRootUrl(env('APP_URL'));
        }
    }

    /**
     * Generate app key if missing and .env exists
     */
    public function genKey(): void
    {
        if (is_file(base_path('.env'))) {
            if (empty(env('APP_KEY'))) {
                Artisan::call('key:generate', ['--force' => true, '--no-interaction' => true]);
            }
        }
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->isLocal()) {
            $this->app->register(IdeHelperServiceProvider::class);
        }

        $this->app->singleton('settings', function () {
            return new Setting();
        });
    }

    /**
     * Check if database needs an update or do first time database setup
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setupDatabase(): void
    {
        $db_type = config()->get('database.default');

        if ($db_type == 'sqlite') {
            $db_file = database_path(env('DB_DATABASE', 'app.sqlite'));
            if (! is_file($db_file)) {
                touch($db_file);
            }
        }

        if ($this->needsDBUpdate()) {
            Artisan::call('migrate', ['--path' => 'database/migrations', '--force' => true, '--seed' => true]);
            ProcessApps::dispatchSync();
            $this->updateApps();
        }
    }

    public function createEnvFile(): void
    {
        if (!is_file(base_path('.env'))) {
            copy(base_path('.env.example'), base_path('.env'));
        }

        $this->genKey();
    }

    private function needsDBUpdate(): bool
    {
        if (!Schema::hasTable('settings')) {
            return true;
        }

        $db_version = Setting::_fetch('version');
        $app_version = config('app.version');

        return version_compare($app_version, $db_version) === 1;
    }

    private function updateApps(): void
    {
        // This lock ensures that the job is not invoked multiple times.
        // In 5 minutes all app updates should be finished.
        $lock = Cache::lock('updateApps', 5*60);

        if ($lock->get()) {
            UpdateApps::dispatchAfterResponse();
        }
    }
}
