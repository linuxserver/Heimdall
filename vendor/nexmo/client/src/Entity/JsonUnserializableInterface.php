<?php
/**
 * Nexmo Client Library for PHP
 *
 * @copyright Copyright (c) 2016 Nexmo, Inc. (http://nexmo.com)
 * @license   https://github.com/Nexmo/nexmo-php/blob/master/LICENSE.txt MIT License
 */

namespace Nexmo\Entity;

/**
 * Identifies the Entity as using JsonSerializable to prepare request data.
 */
interface JsonUnserializableInterface
{
    /**
     * Update the object state with the json data (as an array)
     * @param $json
     * @return null
     */
    public function jsonUnserialize(array $json);
}