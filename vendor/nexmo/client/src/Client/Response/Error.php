<?php
/**
 * Nexmo Client Library for PHP
 *
 * @copyright Copyright (c) 2016 Nexmo, Inc. (http://nexmo.com)
 * @license   https://github.com/Nexmo/nexmo-php/blob/master/LICENSE.txt MIT License
 */

namespace Nexmo\Client\Response;


class Error extends Response
{
    public function __construct($data)
    {
        //normalize the data
        if(isset($data['error_text'])){
            $data['error-text'] = $data['error_text'];
        }

        $this->expected = ['status', 'error-text'];

        return parent::__construct($data);
    }

    public function isError()
    {
        return true;
    }

    public function isSuccess()
    {
        return false;
    }

    public function getCode()
    {
        return $this->data['status'];
    }

    public function getMessage()
    {
        return $this->data['error-text'];
    }
}