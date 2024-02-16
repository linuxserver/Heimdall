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
use Github\HttpClient\Builder;
use GrahamCampbell\GitHub\Auth\Authenticator\AuthenticatorInterface;
use GrahamCampbell\GitHub\Auth\AuthenticatorFactory;
use GrahamCampbell\GitHub\Cache\ConnectionFactory;
use GrahamCampbell\GitHub\HttpClient\BuilderFactory;
use Http\Client\Common\Plugin\RetryPlugin;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use Symfony\Component\Cache\Adapter\Psr16Adapter;

/**
 * This is the github factory class.
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
class GitHubFactory
{
    /**
     * The http client builder factory instance.
     *
     * @var \GrahamCampbell\GitHub\HttpClient\BuilderFactory
     */
    private BuilderFactory $builder;

    /**
     * The authenticator factory instance.
     *
     * @var \GrahamCampbell\GitHub\Auth\AuthenticatorFactory
     */
    private AuthenticatorFactory $auth;

    /**
     * The cache factory instance.
     *
     * @var \GrahamCampbell\GitHub\Cache\ConnectionFactory
     */
    private ConnectionFactory $cache;

    /**
     * Create a new github factory instance.
     *
     * @param \GrahamCampbell\GitHub\HttpClient\BuilderFactory $builder
     * @param \GrahamCampbell\GitHub\Auth\AuthenticatorFactory $auth
     * @param \GrahamCampbell\GitHub\Cache\ConnectionFactory   $cache
     *
     * @return void
     */
    public function __construct(BuilderFactory $builder, AuthenticatorFactory $auth, ConnectionFactory $cache)
    {
        $this->builder = $builder;
        $this->auth = $auth;
        $this->cache = $cache;
    }

    /**
     * Make a new github client.
     *
     * @param string[] $config
     *
     * @throws \InvalidArgumentException
     *
     * @return \Github\Client
     */
    public function make(array $config): Client
    {
        $client = new Client($this->getBuilder($config), Arr::get($config, 'version'), Arr::get($config, 'enterprise'));

        if (!array_key_exists('method', $config)) {
            throw new InvalidArgumentException('The github factory requires an auth method.');
        }

        if ($config['method'] === 'none') {
            return $client;
        }

        return $this->getAuthenticator($config['method'])->with($client)->authenticate($config);
    }

    /**
     * Get the http client builder.
     *
     * @param string[] $config
     *
     * @return \Github\HttpClient\Builder
     */
    protected function getBuilder(array $config): Builder
    {
        $builder = $this->builder->make();

        if ($backoff = Arr::get($config, 'backoff')) {
            $builder->addPlugin(new RetryPlugin(['retries' => $backoff === true ? 2 : $backoff]));
        }

        if (is_array($cache = Arr::get($config, 'cache', false))) {
            $boundedCache = $this->cache->make($cache);

            $builder->addCache(
                new Psr16Adapter($boundedCache),
                ['cache_lifetime' => $boundedCache->getMaximumLifetime()]
            );
        }

        return $builder;
    }

    /**
     * Get the authenticator.
     *
     * @throws \InvalidArgumentException
     *
     * @return \GrahamCampbell\GitHub\Auth\Authenticator\AuthenticatorInterface
     */
    protected function getAuthenticator(string $method): AuthenticatorInterface
    {
        return $this->auth->make($method);
    }
}
