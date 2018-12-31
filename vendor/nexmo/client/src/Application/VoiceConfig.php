<?php
/**
 * Nexmo Client Library for PHP
 *
 * @copyright Copyright (c) 2016 Nexmo, Inc. (http://nexmo.com)
 * @license   https://github.com/Nexmo/nexmo-php/blob/master/LICENSE.txt MIT License
 */

namespace Nexmo\Application;

class VoiceConfig
{
    const EVENT  = 'event_url';
    const ANSWER = 'answer_url';

    protected $webhooks = [];

    public function setWebhook($type, $url, $method = null)
    {
        if(!($url instanceof Webhook)){
            $url = new Webhook($url, $method);
        }

        $this->webhooks[$type] = $url;
        return $this;
    }

    public function getWebhook($type)
    {
        if(isset($this->webhooks[$type])){
            return $this->webhooks[$type];
        }
    }
}