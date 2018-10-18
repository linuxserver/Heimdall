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
use GrahamCampbell\GitHub\Http\ClientBuilder;
use Http\Client\Common\Plugin\RetryPlugin;
use Illuminate\Contracts\Cache\Factory;
use InvalidArgumentException;
use Madewithlove\IlluminatePsrCacheBridge\Laravel\CacheItemPool;

/**
 * This is the github factory class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class GitHubFactory
{
    /**
     * The authenticator factory instance.
     *
     * @var \GrahamCampbell\GitHub\Authenticators\AuthenticatorFactory
     */
    protected $auth;

    /**
     * The illuminate cache instance.
     *
     * @var \Illuminate\Contracts\Cache\Factory|null
     */
    protected $cache;

    /**
     * Create a new github factory instance.
     *
     * @param \GrahamCampbell\GitHub\Authenticators\AuthenticatorFactory $auth
     * @param \Illuminate\Contracts\Cache\Factory|null                   $cache
     *
     * @return void
     */
    public function __construct(AuthenticatorFactory $auth, Factory $cache = null)
    {
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
    public function make(array $config)
    {
        $client = new Client($this->getBuilder($config), array_get($config, 'version'), array_get($config, 'enterprise'));

        if (!array_key_exists('method', $config)) {
            throw new InvalidArgumentException('The github factory requires an auth method.');
        }

        if ($config['method'] === 'none') {
            return $client;
        }

        return $this->auth->make($config['method'])->with($client)->authenticate($config);
    }

    /**
     * Get the http client builder.
     *
     * @param string[] $config
     *
     * @return \GrahamCampbell\GitHub\Http\ClientBuilder
     */
    protected function getBuilder(array $config)
    {
        $builder = new ClientBuilder();

        if ($backoff = array_get($config, 'backoff')) {
            $builder->addPlugin(new RetryPlugin(['retries' => $backoff === true ? 2 : $backoff]));
        }

        if ($this->cache && class_exists(CacheItemPool::class) && $cache = array_get($config, 'cache')) {
            $builder->addCache(new CacheItemPool($this->cache->store($cache === true ? null : $cache)));
        }

        return $builder;
    }
}
