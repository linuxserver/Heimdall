<?php
/**
 * Nexmo Client Library for PHP
 *
 * @copyright Copyright (c) 2017 Nexmo, Inc. (http://nexmo.com)
 * @license   https://github.com/Nexmo/nexmo-php/blob/master/LICENSE.txt MIT License
 */

namespace Nexmo\Call;


class Transfer implements \JsonSerializable
{
    protected $urls;

    public function __construct($urls)
    {
        if(!is_array($urls)){
            $urls = array($urls);
        }

        $this->urls = $urls;
    }

    function jsonSerialize()
    {
        return [
            'action' => 'transfer',
            'destination' => [
                'type' => 'ncco',
                'url' => $this->urls
            ]
        ];
    }
}