<?php
/**
 * Nexmo Client Library for PHP
 *
 * @copyright Copyright (c) 2016 Nexmo, Inc. (http://nexmo.com)
 * @license   https://github.com/Nexmo/nexmo-php/blob/master/LICENSE.txt MIT License
 */

namespace Nexmo\Call;


class Webhook implements \JsonSerializable
{
    protected $urls;

    protected $method;

    protected $type;

    public function __construct($type, $urls, $method = null)
    {
        if(!is_array($urls)){
            $urls = [$urls];
        }

        $this->urls = $urls;
        $this->type = $type;
        $this->method = $method;
    }

    public function getType()
    {
        return $this->type;
    }

    public function add($url)
    {
        $this->urls[] = $url;
    }

    function jsonSerialize()
    {
        $data = [
            $this->type . '_url' => $this->urls
        ];

        if(isset($this->method)){
            $data[$this->type . '_method'] = $this->method;
        }

        return $data;
    }


}