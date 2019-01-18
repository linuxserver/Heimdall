<?php
/**
 * Nexmo Client Library for PHP
 *
 * @copyright Copyright (c) 2016 Nexmo, Inc. (http://nexmo.com)
 * @license   https://github.com/Nexmo/nexmo-php/blob/master/LICENSE.txt MIT License
 */

namespace Nexmo\Voice\Message;
use Nexmo\Client\Request\AbstractRequest;
use Nexmo\Client\Request\RequestInterface;

class Message extends AbstractRequest implements RequestInterface
{
    protected $params = array();

    public function __construct($text, $to, $from = null)
    {
        $this->params['text'] = $text;
        $this->params['to'] = $to;
        $this->params['from'] = $from;
    }

    public function setLanguage($lang)
    {
        $this->params['lg'] = $lang;
        return $this;
    }

    public function setVoice($voice)
    {
        $this->params['voice'] = $voice;
        return $this;
    }

    public function setRepeat($count)
    {
        $this->params['repeat'] = (int) $count;
        return $this;
    }
    public function setCallback($url, $method = null)
    {
        $this->params['callback'] = $url;
        if(!is_null($method)){
            $this->params['callback_method'] = $method;
        } else {
            unset($this->params['callback_method']);
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
        return '/tts/json';
    }

} 