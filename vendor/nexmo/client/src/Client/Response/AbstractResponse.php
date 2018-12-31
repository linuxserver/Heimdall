<?php
/**
 * Nexmo Client Library for PHP
 *
 * @copyright Copyright (c) 2016 Nexmo, Inc. (http://nexmo.com)
 * @license   https://github.com/Nexmo/nexmo-php/blob/master/LICENSE.txt MIT License
 */

namespace Nexmo\Client\Response;


abstract class AbstractResponse implements ResponseInterface
{
    protected $data;

    public function getData()
    {
        return $this->data;
    }

    public function isSuccess()
    {
        return isset($this->data['status']) AND $this->data['status'] == 0;
    }

    public function isError()
    {
        return !$this->isSuccess();
    }
}