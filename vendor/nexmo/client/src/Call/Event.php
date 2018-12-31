<?php
/**
 * Nexmo Client Library for PHP
 *
 * @copyright Copyright (c) 2017 Nexmo, Inc. (http://nexmo.com)
 * @license   https://github.com/Nexmo/nexmo-php/blob/master/LICENSE.txt MIT License
 */

namespace Nexmo\Call;

class Event implements \ArrayAccess
{
    protected $data;

    public function __construct($data)
    {
        if(!isset($data['uuid']) || !isset($data['message'])){
            throw new \InvalidArgumentException('missing message or uuid');
        }

        $this->data = $data;
    }

    public function getId()
    {
        return $this->data['uuid'];
    }

    public function getMessage()
    {
        return $this->data['message'];
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
        throw new \RuntimeException('can not set properties directly');
    }

    public function offsetUnset($offset)
    {
        throw new \RuntimeException('can not set properties directly');
    }
}