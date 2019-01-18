<?php

namespace Nexmo\Account;

use ArrayAccess;
use Nexmo\Client\Exception\Exception;
use Nexmo\Network;
use Nexmo\Entity\EntityInterface;
use Nexmo\Entity\JsonSerializableInterface;
use Nexmo\Entity\JsonResponseTrait;
use Nexmo\Entity\JsonSerializableTrait;
use Nexmo\Entity\NoRequestResponseTrait;
use Nexmo\Entity\JsonUnserializableInterface;

abstract class Price implements EntityInterface, JsonSerializableInterface, JsonUnserializableInterface, ArrayAccess {
    use JsonSerializableTrait;
    use NoRequestResponseTrait;
    use JsonResponseTrait;

    protected $data = [];

    public function getCountryCode()
    {
        return $this['country_code'];
    }

    public function getCountryDisplayName()
    {
        return $this['country_display_name'];
    }

    public function getCountryName()
    {
        return $this['country_name'];
    }

    public function getDialingPrefix()
    {
        return $this['dialing_prefix'];
    }

    public function getDefaultPrice()
    {
        if (isset($this['default_price'])) {
            return $this['default_price'];
        }

        return $this['mt'];
    }

    public function getCurrency()
    {
        return $this['currency'];
    }

    public function getNetworks()
    {
        return $this['networks'];
    }

    public function getPriceForNetwork($networkCode)
    {
        $networks = $this->getNetworks();
        if (isset($networks[$networkCode]))
        {
            return $networks[$networkCode]->{$this->priceMethod}();
        }

        return $this->getDefaultPrice();
    }

    public function jsonUnserialize(array $json)
    {
        // Convert CamelCase to snake_case as that's how we use array access in every other object
        $data = [];
        foreach ($json as $k => $v){
            $k = ltrim(strtolower(preg_replace('/[A-Z]([A-Z](?![a-z]))*/', '_$0', $k)), '_');

            // PrefixPrice fixes
            if ($k == 'country') {
                $k = 'country_code';
            }

            if ($k == 'name') {
                $data['country_display_name'] = $v;
                $data['country_name'] = $v;
            }

            if ($k == 'prefix') {
                $k = 'dialing_prefix';
            }

            $data[$k] = $v;
        }

        // Create objects for all the nested networks too
        $networks = [];
        if (isset($json['networks'])) {
            foreach ($json['networks'] as $n){
                if (isset($n['code'])) {
                    $n['networkCode'] = $n['code'];
                    unset ($n['code']);
                }

                if (isset($n['network'])) {
                    $n['networkName'] = $n['network'];
                    unset ($n['network']);
                }

                $network = new Network($n['networkCode'], $n['networkName']);
                $network->jsonUnserialize($n);
                $networks[$network->getCode()] = $network;
            }
        }

        $data['networks'] = $networks;
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
        throw new Exception('Price is read only');
    }

    public function offsetUnset($offset)
    {
        throw new Exception('Price is read only');
    }
}
