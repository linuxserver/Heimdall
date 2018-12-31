<?php

namespace Nexmo;

use ArrayAccess;
use Nexmo\Client\Exception\Exception;
use Nexmo\Entity\EntityInterface;
use Nexmo\Entity\JsonSerializableInterface;
use Nexmo\Entity\JsonResponseTrait;
use Nexmo\Entity\JsonSerializableTrait;
use Nexmo\Entity\NoRequestResponseTrait;
use Nexmo\Entity\JsonUnserializableInterface;

class Network implements EntityInterface, JsonSerializableInterface, JsonUnserializableInterface, ArrayAccess {
    use JsonSerializableTrait;
    use NoRequestResponseTrait;
    use JsonResponseTrait;

    protected $data = [];

    public function __construct($networkCode, $networkName)
    {
        $this->data['network_code'] = $networkCode;
        $this->data['network_name'] = $networkName;
    }

    public function getCode()
    {
        return $this['network_code'];
    }

    public function getName()
    {
        return $this['network_name'];
    }

    public function getOutboundSmsPrice()
    {
        if (isset($this['sms_price'])) {
            return $this['sms_price'];
        }
        return $this['price'];
    }

    public function getOutboundVoicePrice()
    {
        if (isset($this['voice_price'])) {
            return $this['voice_price'];
        }
        return $this['price'];
    }

    public function getPrefixPrice() {
        return $this['mt_price'];
    }

    public function getCurrency()
    {
        return $this['currency'];
    }

    public function jsonUnserialize(array $json)
    {
        // Convert CamelCase to snake_case as that's how we use array access in every other object
        $data = [];
        foreach ($json as $k => $v){
            $k = ltrim(strtolower(preg_replace('/[A-Z]([A-Z](?![a-z]))*/', '_$0', $k)), '_');
            $data[$k] = $v;
        }
        $this->data = $data;
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
        throw new Exception('Network is read only');
    }

    public function offsetUnset($offset)
    {
        throw new Exception('Network is read only');
    }
}
