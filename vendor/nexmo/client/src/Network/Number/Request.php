<?php
/**
 * Nexmo Client Library for PHP
 *
 * @copyright Copyright (c) 2016 Nexmo, Inc. (http://nexmo.com)
 * @license   https://github.com/Nexmo/nexmo-php/blob/master/LICENSE.txt MIT License
 */

namespace Nexmo\Network\Number;

use Nexmo\Client\Request\AbstractRequest;
use Nexmo\Client\Request\RequestInterface;
use Nexmo\Client\Request\WrapResponseInterface;
use Nexmo\Client\Response\Error;
use Nexmo\Client\Response\ResponseInterface;

class Request extends AbstractRequest implements RequestInterface, WrapResponseInterface
{
    const FEATURE_TYPE = 'type';
    const FEATURE_VALID = 'valid';
    const FEATURE_REACHABLE = 'reachable';
    const FEATURE_CARRIER = 'carrier';
    const FEATURE_PORTED = 'ported';
    const FEATURE_ROAMING = 'roaming';
    const FEATURE_SUBSCRIBER = 'subscriber';

    protected $params = array();

    public function __construct($number, $callback, $features = array(), $timeout = null, $method = null, $ref = null)
    {
        $this->params['number'] = $number;
        $this->params['callback'] = $callback;
        $this->params['callback_timeout'] = $timeout;
        $this->params['callback_method'] = $method;
        $this->params['client_ref'] = $ref;

        if(!empty($features)){
            $this->params['features'] = implode(',', $features);
        }
    }

    public function getURI()
    {
        return '/ni/json';
    }

    /**
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function wrapResponse(ResponseInterface $response)
    {
        if($response->isError()){
            return new Error($response->getData());
        }

        return new Response($response->getData());
    }


}