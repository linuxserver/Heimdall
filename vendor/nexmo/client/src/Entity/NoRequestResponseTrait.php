<?php
/**
 * Nexmo Client Library for PHP
 *
 * @copyright Copyright (c) 2016 Nexmo, Inc. (http://nexmo.com)
 * @license   https://github.com/Nexmo/nexmo-php/blob/master/LICENSE.txt MIT License
 */

namespace Nexmo\Entity;

/**
 * Class Psr7Trait
 *
 * Allow an entity to contain last request / response objects.
 */
trait NoRequestResponseTrait
{
    public function setResponse(\Psr\Http\Message\ResponseInterface $response)
    {
        throw new \RuntimeException(__CLASS__ . ' does not support request / response');
    }

    public function setRequest(\Psr\Http\Message\RequestInterface $request)
    {
        throw new \RuntimeException(__CLASS__ . ' does not support request / response');
    }

    public function getRequest()
    {
        return null;
    }

    public function getResponse()
    {
        return null;
    }
}