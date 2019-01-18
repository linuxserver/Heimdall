<?php
/**
 * Nexmo Client Library for PHP
 *
 * @copyright Copyright (c) 2016 Nexmo, Inc. (http://nexmo.com)
 * @license   https://github.com/Nexmo/nexmo-php/blob/master/LICENSE.txt MIT License
 */

namespace Nexmo\Entity;

/**
 * Implements getRequestData from EntityInterface based on the entity's jsonSerialize().
 *
 * @see EntityInterface::getRequestData()
 */
trait JsonSerializableTrait
{
    /**
     * Get an array of params to use in an API request.
     */
    public function getRequestData($sent = true)
    {
        if(!($this instanceof EntityInterface)){
            throw new \Exception(sprintf(
                '%s can only be used if the class implements %s',
                __TRAIT__,
                EntityInterface::class
            ));
        }

        if(!($this instanceof \JsonSerializable)){
            throw new \Exception(sprintf(
                '%s can only be used if the class implements %s',
                __TRAIT__,
                \JsonSerializable::class
            ));
        }

        if($sent && ($request = $this->getRequest())){
            //TODO, figure out what the request data actually was
        }

        return $this->jsonSerialize();
    }
}