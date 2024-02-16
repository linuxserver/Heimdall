<?php

declare(strict_types=1);

/*
 * This file is part of Laravel GitHub.
 *
 * (c) Graham Campbell <hello@gjcampbell.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\GitHub;

use Github\Client;
use GrahamCampbell\GitHub\Auth\AuthenticatorFactory;
use GrahamCampbell\GitHub\Cache\ConnectionFactory;
use GrahamCampbell\GitHub\HttpClient\BuilderFactory;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\HttpFactory as GuzzlePsrFactory;
use Illuminate\Contracts\Container\Container;
use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application as LumenApplication;

/**
 * This is the github service provider class.
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
class GitHubServiceProvider extends ServiceProvider
{
    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->setupConfig();
    }

    /**
     * Setup the config.
     *
     * @return void
     */
    private function setupConfig(): void
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
    public function register(): void
    {
        $this->registerHttpClientFactory();
        $this->registerAuthFactory();
        $this->registerCacheFactory();
        $this->registerGitHubFactory();
        $this->registerManager();
        $this->registerBindings();
    }

    /**
     * Register the http client factory class.
     *
     * @return void
     */
    private function registerHttpClientFactory(): void
    {
        $this->app->singleton('github.httpclientfactory', function (): BuilderFactory {
            $psrFactory = new GuzzlePsrFactory();

            return new BuilderFactory(
                new GuzzleClient(['connect_timeout' => 10, 'timeout' => 30]),
                $psrFactory,
                $psrFactory,
            );
        });

        $this->app->alias('github.httpclientfactory', BuilderFactory::class);
    }

    /**
     * Register the auth factory class.
     *
     * @return void
     */
    private function registerAuthFactory(): void
    {
        $this->app->singleton('github.authfactory', function (): AuthenticatorFactory {
            return new AuthenticatorFactory();
        });

        $this->app->alias('github.authfactory', AuthenticatorFactory::class);
    }

    /**
     * Register the cache factory class.
     *
     * @return void
     */
    private function registerCacheFactory(): void
    {
        $this->app->singleton('github.cachefactory', function (Container $app): ConnectionFactory {
            $cache = $app->bound('cache') ? $app->make('cache') : null;

            return new ConnectionFactory($cache);
        });

        $this->app->alias('github.cachefactory', ConnectionFactory::class);
    }

    /**
     * Register the github factory class.
     *
     * @return void
     */
    private function registerGitHubFactory(): void
    {
        $this->app->singleton('github.factory', function (Container $app): GitHubFactory {
            $builder = $app['github.httpclientfactory'];
            $auth = $app['github.authfactory'];
            $cache = $app['github.cachefactory'];

            return new GitHubFactory($builder, $auth, $cache);
        });

        $this->app->alias('github.factory', GitHubFactory::class);
    }

    /**
     * Register the manager class.
     *
     * @return void
     */
    private function registerManager(): void
    {
        $this->app->singleton('github', function (Container $app): GitHubManager {
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
    private function registerBindings(): void
    {
        $this->app->bind('github.connection', function (Container $app): Client {
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
    public function provides(): array
    {
        return [
            'github.httpclientfactory',
            'github.authfactory',
            'github.cachefactory',
            'github.factory',
            'github',
            'github.connection',
        ];
    }
}
