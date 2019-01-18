<?php
/**
 * Nexmo Client Library for PHP
 *
 * @copyright Copyright (c) 2016 Nexmo, Inc. (http://nexmo.com)
 * @license   https://github.com/Nexmo/nexmo-php/blob/master/LICENSE.txt MIT License
 */

namespace Nexmo\Client\Factory;
use Nexmo\Client;

class MapFactory implements FactoryInterface
{
    /**
     * Map of api namespaces to classes.
     *
     * @var array
     */
    protected $map = [];

    /**
     * Map of instances.
     *
     * @var array
     */
    protected $cache = [];

    /**
     * Nexmo Client
     *
     * @var Client
     */
    protected $client;

    public function __construct($map, Client $client)
    {
        $this->map = $map;
        $this->client = $client;
    }

    public function hasApi($api)
    {
        return isset($this->map[$api]);
    }

    public function getApi($api)
    {
        if(isset($this->cache[$api])){
            return $this->cache[$api];
        }

        if(!$this->hasApi($api)){
            throw new \RuntimeException(sprintf(
                'no map defined for `%s`',
                $api
            ));
        }

        $class = $this->map[$api];

        $instance = new $class();
        if($instance instanceof Client\ClientAwareInterface){
            $instance->setClient($this->client);
        }
        $this->cache[$api] = $instance;
        return $instance;
    }
}