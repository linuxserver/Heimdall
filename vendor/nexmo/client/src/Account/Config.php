<?php

namespace Nexmo\Account;

use ArrayAccess;
use Nexmo\Client\Exception\Exception;
use Nexmo\Entity\JsonSerializableInterface;
use Nexmo\Entity\JsonUnserializableInterface;

class Config implements JsonSerializableInterface, JsonUnserializableInterface, ArrayAccess {
    public function __construct($sms_callback_url = null, $dr_callback_url = null, $max_outbound_request = null, $max_inbound_request = null, $max_calls_per_second = null)
    {
        if(!is_null($sms_callback_url)) {
            $this->data['sms_callback_url'] = $sms_callback_url;
        }
        if(!is_null($dr_callback_url)) {
            $this->data['dr_callback_url'] = $dr_callback_url;
        }
        if(!is_null($max_outbound_request)) {
            $this->data['max_outbound_request'] = $max_outbound_request;
        }
        if(!is_null($max_inbound_request)) {
            $this->data['max_inbound_request'] = $max_inbound_request;
        }
        if(!is_null($max_calls_per_second)) {
            $this->data['max_calls_per_second'] = $max_calls_per_second;
        }
    }

    public function getSmsCallbackUrl()
    {
        return $this['sms_callback_url'];
    }

    public function getDrCallbackUrl()
    {
        return $this['dr_callback_url'];
    }

    public function getMaxOutboundRequest()
    {
        return $this['max_outbound_request'];
    }

    public function getMaxInboundRequest()
    {
        return $this['max_inbound_request'];
    }

    public function getMaxCallsPerSecond()
    {
        return $this['max_calls_per_second'];
    }

    public function jsonUnserialize(array $json)
    {
        $this->data = [
            'sms_callback_url' => $json['sms_callback_url'],
            'dr_callback_url' => $json['dr_callback_url'],
            'max_outbound_request' => $json['max_outbound_request'],
            'max_inbound_request' => $json['max_inbound_request'],
            'max_calls_per_second' => $json['max_calls_per_second'],
        ];
    }

    function jsonSerialize()
    {
        return $this->data;
    }

    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->data[$offset];
    }

    public function offsetSet($offset, $value)
    {
        throw new Exception('Balance is read only');
    }

    public function offsetUnset($offset)
    {
        throw new Exception('Balance is read only');
    }
}

