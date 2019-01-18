<?php
/**
 * Nexmo Client Library for PHP
 *
 * @copyright Copyright (c) 2016 Nexmo, Inc. (http://nexmo.com)
 * @license   https://github.com/Nexmo/nexmo-php/blob/master/LICENSE.txt MIT License
 */

namespace Nexmo\Application;

class Webhook
{
    const METHOD_POST = 'POST';
    const METHOD_GET  = 'GET';

    /**
     * @var string;
     */
    protected $method;

    /**
     * @var string
     */
    protected $url;
    
    public function __construct($url, $method = self::METHOD_POST)
    {
        $this->url = $url;
        $this->method = $method;
    }

    public function getMethod()
    {
        return $this->method;
    }
    
    public function getUrl()
    {
        return $this->url;
    }
    
    public function __toString()
    {
        return $this->getUrl();
    }
}