<?php
/**
 * Nexmo Client Library for PHP
 *
 * @copyright Copyright (c) 2016 Nexmo, Inc. (http://nexmo.com)
 * @license   https://github.com/Nexmo/nexmo-php/blob/master/LICENSE.txt MIT License
 */

namespace Nexmo\Call;

/**
 * Class Endpoint
 * Represents a call destination / origin.
 *
 * TODO: Try to unify this and other (message, etc) endpoint identifiers.
 */
class Endpoint implements \JsonSerializable
{
    const PHONE = 'phone';

    protected $id;

    protected $type;

    protected $additional;

    public function __construct($id, $type = self::PHONE, $additional = [])
    {
        $this->id = $id;
        $this->type = $type;
        $this->additional = $additional;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getId()
    {
        return $this->id;
    }

    public function set($property, $value)
    {
        $this->additional[$property] = $value;
        return $this;
    }

    public function get($property)
    {
        if(isset($this->additional[$property])){
            return $this->additional[$property];
        }
    }

    public function getNumber()
    {
        if(!self::PHONE == $this->type){
            throw new \RuntimeException('number not defined for this type');
        }

        return $this->getId();
    }

    public function __toString()
    {
        return (string) $this->getId();
    }

    function jsonSerialize()
    {
        switch($this->type){
            case 'phone':
                return array_merge(
                    $this->additional,
                    [
                        'type' => $this->type,
                        'number' => $this->id
                    ]

                );
            default:
                throw new \RuntimeException('unknown type: ' . $this->type);
        }
    }
}