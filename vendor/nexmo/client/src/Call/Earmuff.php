<?php
/**
 * Nexmo Client Library for PHP
 *
 * @copyright Copyright (c) 2017 Nexmo, Inc. (http://nexmo.com)
 * @license   https://github.com/Nexmo/nexmo-php/blob/master/LICENSE.txt MIT License
 */

namespace Nexmo\Call;


class Earmuff implements \JsonSerializable
{
    function jsonSerialize()
    {
        return [
            'action' => 'earmuff'
        ];
    }
}