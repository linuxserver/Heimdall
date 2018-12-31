<?php

namespace Nexmo\Account;

use ArrayAccess;
use Nexmo\Client\Exception\Exception;
use Nexmo\Entity\JsonSerializableInterface;
use Nexmo\Entity\JsonUnserializableInterface;

class Balance implements JsonSerializableInterface, JsonUnserializableInterface, ArrayAccess {
    public function __construct($balance, $autoReload)
    {
        $this->data['balance'] = $balance;
        $this->data['auto_reload'] = $autoReload;
    }

    public function getBalance()
    {
        return $this['balance'];
    }

    public function getAutoReload()
    {
        return $this['auto_reload'];
    }

    public function jsonUnserialize(array $json)
    {
        $this->data = [
            'balance' => $json['value'],
            'auto_reload' => $json['autoReload']
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