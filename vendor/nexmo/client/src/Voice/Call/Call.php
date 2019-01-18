<?php
/**
 * Nexmo Client Library for PHP
 *
 * @copyright Copyright (c) 2016 Nexmo, Inc. (http://nexmo.com)
 * @license   https://github.com/Nexmo/nexmo-php/blob/master/LICENSE.txt MIT License
 */

namespace Nexmo\Voice\Call;
use Nexmo\Client\Request\AbstractRequest;
use Nexmo\Client\Request\RequestInterface;

class Call extends AbstractRequest implements RequestInterface
{
    public function __construct($url, $to, $from = null)
    {
        $this->params['answer_url'] = $url;
        $this->params['to'] = $to;

        if(!is_null($from)){
            $this->params['from'] = $from;
        }
    }

    public function setAnswer($url, $method = null)
    {
        $this->params['answer_url'] = $url;
        if(!is_null($method)){
            $this->params['answer_method'] = $method;
        } else {
            unset($this->params['answer_method']);
        }

        return $this;
    }

    public function setError($url, $method = null)
    {
        $this->params['error_url'] = $url;
        if(!is_null($method)){
            $this->params['error_method'] = $method;
        } else {
            unset($this->params['error_method']);
        }

        return $this;
    }

    public function setStatus($url, $method = null)
    {
        $this->params['status_url'] = $url;
        if(!is_null($method)){
            $this->params['status_method'] = $method;
        } else {
            unset($this->params['status_method']);
        }

        return $this;
    }


    public function setMachineDetection($hangup = true, $timeout = null)
    {
        $this->params['machine_detection'] = ($hangup ? 'hangup' : 'true');
        if(!is_null($timeout)){
            $this->params['machine_timeout'] = (int) $timeout;
        } else {
            unset($this->params['machine_timeout']);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getURI()
    {
        return '/call/json';
    }

} 