<?php

declare(strict_types=1);

/*
 * This file is part of Laravel GitHub.
 *
 * (c) Graham Campbell <graham@alt-three.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\GitHub;

use Github\Client;
use GrahamCampbell\GitHub\Authenticators\AuthenticatorFactory;
use Illuminate\Contracts\Container\Container;
use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application as LumenApplication;

/**
 * This is the github service provider class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class GitHubServiceProvider extends ServiceProvider
{
    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->setupConfig();
    }

    /**
     * Setup the config.
     *
     * @return void
     */
    protected function setupConfig()
    {
        $source = realpath($raw = __DIR__.'/../config/github.php') ?: $raw;

        if ($this->app instanceof LaravelApplication && $this->app->runningInConsole()) {
            $this->publishes([$source => config_path('github.php')]);
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('github');
        }

        $this->mergeConfigFrom($source, 'github');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerAuthFactory();
        $this->registerGitHubFactory();
        $this->registerManager();
        $this->registerBindings();
    }

    /**
     * Register the auth factory class.
     *
     * @return void
     */
    protected function registerAuthFactory()
    {
        $this->app->singleton('github.authfactory', function () {
            return new AuthenticatorFactory();
        });

        $this->app->alias('github.authfactory', AuthenticatorFactory::class);
    }

    /**
     * Register the github factory class.
     *
     * @return void
     */
    protected function registerGitHubFactory()
    {
        $this->app->singleton('github.factory', function (Container $app) {
            $auth = $app['github.authfactory'];
            $cache = $app['cache'];

            return new GitHubFactory($auth, $cache);
        });

        $this->app->alias('github.factory', GitHubFactory::class);
    }

    /**
     * Register the manager class.
     *
     * @return void
     */
    protected function registerManager()
    {
        $this->app->singleton('github', function (Container $app) {
            $config = $app['config'];
            $factory = $app['github.factory'];

            return new GitHubManager($config, $factory);
        });

        $this->app->alias('github', GitHubManager::class);
    }

    /**
     * Register the bindings.
     *
     * @return void
     */
    protected function registerBindings()
    {
        $this->app->bind('github.connection', function (Container $app) {
            $manager = $app['github'];

            return $manager->connection();
        });

        $this->app->alias('github.connection', Client::class);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return string[]
     */
    public function provides()
    {
        return [
            'github.authfactory',
            'github.factory',
            'github',
            'github.connection',
        ];
    }
}
