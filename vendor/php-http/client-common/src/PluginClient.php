<?php

namespace Http\Client\Common;

use Http\Client\Common\Exception\LoopException;
use Http\Client\Exception as HttplugException;
use Http\Client\HttpAsyncClient;
use Http\Client\HttpClient;
use Http\Client\Promise\HttpFulfilledPromise;
use Http\Client\Promise\HttpRejectedPromise;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * The client managing plugins and providing a decorator around HTTP Clients.
 *
 * @author Joel Wurtz <joel.wurtz@gmail.com>
 */
final class PluginClient implements HttpClient, HttpAsyncClient
{
    /**
     * An HTTP async client.
     *
     * @var HttpAsyncClient
     */
    private $client;

    /**
     * The plugin chain.
     *
     * @var Plugin[]
     */
    private $plugins;

    /**
     * A list of options.
     *
     * @var array
     */
    private $options;

    /**
     * @param HttpClient|HttpAsyncClient|ClientInterface $client
     * @param Plugin[]                                   $plugins
     * @param array                                      $options {
     *
     *     @var int      $max_restarts
     *     @var Plugin[] $debug_plugins an array of plugins that are injected between each normal plugin
     * }
     *
     * @throws \RuntimeException if client is not an instance of HttpClient or HttpAsyncClient
     */
    public function __construct($client, array $plugins = [], array $options = [])
    {
        if ($client instanceof HttpAsyncClient) {
            $this->client = $client;
        } elseif ($client instanceof HttpClient || $client instanceof ClientInterface) {
            $this->client = new EmulatedHttpAsyncClient($client);
        } else {
            throw new \RuntimeException('Client must be an instance of Http\\Client\\HttpClient or Http\\Client\\HttpAsyncClient');
        }

        $this->plugins = $plugins;
        $this->options = $this->configure($options);
    }

    /**
     * {@inheritdoc}
     */
    public function sendRequest(RequestInterface $request)
    {
        // If we don't have an http client, use the async call
        if (!($this->client instanceof HttpClient)) {
            return $this->sendAsyncRequest($request)->wait();
        }

        // Else we want to use the synchronous call of the underlying client, and not the async one in the case
        // we have both an async and sync call
        $pluginChain = $this->createPluginChain($this->plugins, function (RequestInterface $request) {
            try {
                return new HttpFulfilledPromise($this->client->sendRequest($request));
            } catch (HttplugException $exception) {
                return new HttpRejectedPromise($exception);
            }
        });

        return $pluginChain($request)->wait();
    }

    /**
     * {@inheritdoc}
     */
    public function sendAsyncRequest(RequestInterface $request)
    {
        $pluginChain = $this->createPluginChain($this->plugins, function (RequestInterface $request) {
            return $this->client->sendAsyncRequest($request);
        });

        return $pluginChain($request);
    }

    /**
     * Configure the plugin client.
     *
     * @param array $options
     *
     * @return array
     */
    private function configure(array $options = [])
    {
        if (isset($options['debug_plugins'])) {
            @trigger_error('The "debug_plugins" option is deprecated since 1.5 and will be removed in 2.0.', E_USER_DEPRECATED);
        }

        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'max_restarts' => 10,
            'debug_plugins' => [],
        ]);

        $resolver
            ->setAllowedTypes('debug_plugins', 'array')
            ->setAllowedValues('debug_plugins', function (array $plugins) {
                foreach ($plugins as $plugin) {
                    // Make sure each object passed with the `debug_plugins` is an instance of Plugin.
                    if (!$plugin instanceof Plugin) {
                        return false;
                    }
                }

                return true;
            });

        return $resolver->resolve($options);
    }

    /**
     * Create the plugin chain.
     *
     * @param Plugin[] $pluginList     A list of plugins
     * @param callable $clientCallable Callable making the HTTP call
     *
     * @return callable
     */
    private function createPluginChain($pluginList, callable $clientCallable)
    {
        $firstCallable = $lastCallable = $clientCallable;

        /*
         * Inject debug plugins between each plugin.
         */
        $pluginListWithDebug = $this->options['debug_plugins'];
        foreach ($pluginList as $plugin) {
            $pluginListWithDebug[] = $plugin;
            $pluginListWithDebug = array_merge($pluginListWithDebug, $this->options['debug_plugins']);
        }

        while ($plugin = array_pop($pluginListWithDebug)) {
            $lastCallable = function (RequestInterface $request) use ($plugin, $lastCallable, &$firstCallable) {
                return $plugin->handleRequest($request, $lastCallable, $firstCallable);
            };

            $firstCallable = $lastCallable;
        }

        $firstCalls = 0;
        $firstCallable = function (RequestInterface $request) use ($lastCallable, &$firstCalls) {
            if ($firstCalls > $this->options['max_restarts']) {
                throw new LoopException('Too many restarts in plugin client', $request);
            }

            ++$firstCalls;

            return $lastCallable($request);
        };

        return $firstCallable;
    }
}
